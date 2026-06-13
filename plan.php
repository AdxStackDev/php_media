<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Generate a CSRF token for the plan forms if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Plan - IPTV Player</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold">Select Your Plan</h1>
                <div class="text-sm">
                    <span class="text-gray-400">Logged in as:</span>
                    <span class="text-blue-400 ml-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="ml-4 text-red-400 hover:text-red-300">Logout</a>
                </div>
            </div>

<?php
class Plan {
    public $name;
    public $validityDays;
    private $purchaseDate;

    public function __construct($name, $validityDays) {
        $this->name = $name;
        $this->validityDays = $validityDays;
        $this->purchaseDate = null;
    }

    public function purchase() {
        $this->purchaseDate = new DateTime();
        return true;
    }

    public function isValid() {
        if ($this->purchaseDate === null) {
            return false;
        }

        $expirationDate = clone $this->purchaseDate;
        $expirationDate->add(new DateInterval("P{$this->validityDays}D"));

        $currentDate = new DateTime();

        return ($currentDate <= $expirationDate);
    }
    
    public function getExpirationDate() {
        if ($this->purchaseDate === null) {
            return null;
        }
        
        $expirationDate = clone $this->purchaseDate;
        $expirationDate->add(new DateInterval("P{$this->validityDays}D"));
        return $expirationDate;
    }
}

// Process form submission
$planPurchased = false;
$planError = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["plan"])) {
    // Validate CSRF token before processing the purchase
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $planError = "Invalid or expired request. Please try again.";
    } else {
        $selectedPlan = $_POST["plan"];

        switch ($selectedPlan) {
            case "A":
                $plan = new Plan("A", 7);
                $planPrice = "$9.99";
                break;
            case "B":
                $plan = new Plan("B", 30);
                $planPrice = "$29.99";
                break;
            case "C":
                $plan = new Plan("C", 365);
                $planPrice = "$99.99";
                break;
            default:
                echo "<div class='bg-red-500 text-white p-4 rounded'>Invalid plan selection.</div>";
                exit;
        }

        // Purchase and check plan validity
        $plan->purchase();
        $isValid = $plan->isValid();

        // Store plan in session
        $_SESSION['plan'] = $selectedPlan;
        $_SESSION['plan_purchase_date'] = date('Y-m-d H:i:s');
        $_SESSION['plan_validity_days'] = $plan->validityDays;
        // Store an absolute expiry timestamp so expiration can be enforced later
        $_SESSION['plan_expires'] = time() + ($plan->validityDays * 86400);

        $planPurchased = true;
    }
}
?>

            <?php if (isset($_GET['expired'])): ?>
                <div class="bg-yellow-500 text-gray-900 p-4 rounded-lg mb-6">
                    <h2 class="text-xl font-bold mb-1">Your plan has expired</h2>
                    <p>Please choose a plan below to continue watching.</p>
                </div>
            <?php endif; ?>

            <?php if (isset($planError)): ?>
                <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                    <?php echo htmlspecialchars($planError); ?>
                </div>
            <?php endif; ?>

            <?php if ($planPurchased): ?>
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    <h2 class="text-xl font-bold mb-2">✓ Plan Purchased Successfully!</h2>
                    <p>Your subscription is now active.</p>
                    <a href="channel.php" class="inline-block mt-4 bg-white text-green-600 px-6 py-2 rounded font-bold hover:bg-gray-100">
                        Start Watching Channels
                    </a>
                </div>
            <?php endif; ?>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <!-- Plan A -->
                <div class="bg-gray-800 rounded-lg p-6 border-2 border-gray-700 hover:border-blue-500 transition">
                    <h3 class="text-2xl font-bold mb-2">Plan A</h3>
                    <p class="text-gray-400 mb-4">Weekly Access</p>
                    <div class="text-4xl font-bold mb-4">$9.99</div>
                    <ul class="mb-6 space-y-2 text-sm">
                        <li>✓ 7 days validity</li>
                        <li>✓ All channels access</li>
                        <li>✓ HD quality streaming</li>
                        <li>✓ Multiple devices</li>
                    </ul>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="plan" value="A">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Select Plan A
                        </button>
                    </form>
                </div>

                <!-- Plan B -->
                <div class="bg-gray-800 rounded-lg p-6 border-2 border-blue-500 hover:border-blue-400 transition transform scale-105">
                    <div class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full inline-block mb-2">POPULAR</div>
                    <h3 class="text-2xl font-bold mb-2">Plan B</h3>
                    <p class="text-gray-400 mb-4">Monthly Access</p>
                    <div class="text-4xl font-bold mb-4">$29.99</div>
                    <ul class="mb-6 space-y-2 text-sm">
                        <li>✓ 30 days validity</li>
                        <li>✓ All channels access</li>
                        <li>✓ HD quality streaming</li>
                        <li>✓ Multiple devices</li>
                        <li>✓ Priority support</li>
                    </ul>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="plan" value="B">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Select Plan B
                        </button>
                    </form>
                </div>

                <!-- Plan C -->
                <div class="bg-gray-800 rounded-lg p-6 border-2 border-gray-700 hover:border-blue-500 transition">
                    <div class="bg-yellow-500 text-gray-900 text-xs font-bold px-3 py-1 rounded-full inline-block mb-2">BEST VALUE</div>
                    <h3 class="text-2xl font-bold mb-2">Plan C</h3>
                    <p class="text-gray-400 mb-4">Yearly Access</p>
                    <div class="text-4xl font-bold mb-4">$99.99</div>
                    <ul class="mb-6 space-y-2 text-sm">
                        <li>✓ 365 days validity</li>
                        <li>✓ All channels access</li>
                        <li>✓ HD quality streaming</li>
                        <li>✓ Multiple devices</li>
                        <li>✓ Priority support</li>
                        <li>✓ Save 72% annually</li>
                    </ul>
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="plan" value="C">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Select Plan C
                        </button>
                    </form>
                </div>
            </div>

            <?php if (isset($_SESSION['plan'])): ?>
                <div class="bg-gray-800 p-6 rounded-lg">
                    <h2 class="text-xl font-bold mb-4">Your Current Plan</h2>
                    <p><strong>Plan:</strong> <?php echo htmlspecialchars($_SESSION['plan']); ?></p>
                    <p><strong>Purchase Date:</strong> <?php echo htmlspecialchars($_SESSION['plan_purchase_date']); ?></p>
                    <p><strong>Validity:</strong> <?php echo htmlspecialchars($_SESSION['plan_validity_days']); ?> days</p>
                    <a href="channel.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold">
                        Watch Channels
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

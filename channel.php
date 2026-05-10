<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Check if user has an active plan
if (!isset($_SESSION['plan'])) {
    header('Location: plan.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>IPTV Player - Channels</title>
  <link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet">
  <script src="https://vjs.zencdn.net/7.11.4/video.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .channel-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
      padding: 1rem;
    }
    .channel-card {
      transition: transform 0.2s;
    }
    .channel-card:hover {
      transform: scale(1.02);
    }
  </style>
</head>

<body class="bg-gray-900 text-white">
  <!-- Header -->
  <header class="bg-gray-800 shadow-lg">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-bold">IPTV Player</h1>
        <span class="text-sm text-gray-400">Plan: <?php echo htmlspecialchars($_SESSION['plan']); ?></span>
      </div>
      <div class="flex items-center space-x-4">
        <input type="text" id="searchInput" placeholder="Search channels..." 
          class="px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        <span class="text-sm text-gray-400">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="text-sm text-red-400 hover:text-red-300">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 h-screen fixed left-0 top-16 overflow-y-auto">
      <div class="p-4">
        <h2 class="text-lg font-semibold mb-4">Categories</h2>
        <ul class="space-y-2">
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="all">All Channels</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="news">News</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="entertainment">Entertainment</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="movies">Movies</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="music">Music</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="sports">Sports</button></li>
          <li><button class="category-btn w-full text-left px-4 py-2 rounded hover:bg-gray-700" data-category="regional">Regional</button></li>
        </ul>

        <div class="mt-8">
          <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
          <ul class="space-y-2">
            <li><button id="favoritesBtn" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700"><i class="fas fa-heart mr-2"></i>My Favorites</button></li>
            <li><button id="recentBtn" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700"><i class="fas fa-history mr-2"></i>Recently Watched</button></li>
          </ul>
        </div>

        <div class="mt-8">
          <h2 class="text-lg font-semibold mb-4">Settings</h2>
          <ul class="space-y-2">
            <li><button id="qualityBtn" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700"><i class="fas fa-cog mr-2"></i>Quality Settings</button></li>
            <li><button id="themeBtn" class="w-full text-left px-4 py-2 rounded hover:bg-gray-700"><i class="fas fa-palette mr-2"></i>Theme Settings</button></li>
          </ul>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="ml-64 flex-1 p-4">
      <!-- Video Player Section -->
      <div id="videoSection" class="hidden">
        <div class="bg-black rounded-lg overflow-hidden">
          <video id="videoPlayerElement" class="w-full" controls>
            Your browser does not support the video tag.
          </video>
        </div>
        <div class="mt-4 flex justify-between items-center">
          <h2 id="currentChannelName" class="text-xl font-bold"></h2>
          <div class="flex space-x-4">
            <button id="prevChannel" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600"><i class="fas fa-backward mr-2"></i>Previous</button>
            <button id="nextChannel" class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-600">Next<i class="fas fa-forward ml-2"></i></button>
          </div>
        </div>
      </div>

      <!-- Channel Grid -->
      <div id="channelGrid" class="channel-grid mt-4">
        <?php
        $channels = array(
            // News Channels
            "Aaj Tak" => array("url" => "https://aajtaklive-amd.akamaized.net/hls/live/2014416/aajtak/aajtaklive/live_404p/chunks.m3u8", "category" => "news"),
            "ABP News" => array("url" => "https://abplivetv.akamaized.net/hls/live/2043013/abpnews/master.m3u8", "category" => "news"),
            "India TV" => array("url" => "https://indiatv.akamaized.net/hls/live/2014320/indiatv/master.m3u8", "category" => "news"),
            "Zee News" => array("url" => "https://znews.akamaized.net/hls/live/2014310/zeenews/master.m3u8", "category" => "news"),
            "News18 India" => array("url" => "https://n18syndication.akamaized.net/bpk-tv/News18_India_NW18_MOB/output01/index.m3u8", "category" => "news"),
            "Republic TV" => array("url" => "https://republic.akamaized.net/hls/live/2018070/republic/master.m3u8", "category" => "news"),
            "Times Now" => array("url" => "https://timesnow.akamaized.net/hls/live/2018071/timesnow/master.m3u8", "category" => "news"),
            "NDTV India" => array("url" => "https://ndtv.akamaized.net/hls/live/2018072/ndtv/master.m3u8", "category" => "news"),
            "News Nation" => array("url" => "https://newsnation.akamaized.net/hls/live/2018073/newsnation/master.m3u8", "category" => "news"),
            "CNBC TV18" => array("url" => "https://cnbctv18.akamaized.net/hls/live/2018074/cnbctv18/master.m3u8", "category" => "news"),

            // Entertainment Channels
            "Sony SAB" => array("url" => "https://sonysab.akamaized.net/hls/live/2018077/sonysab/master.m3u8", "category" => "entertainment"),
            "Sony Pal" => array("url" => "https://sonypal.akamaized.net/hls/live/2018078/sonypal/master.m3u8", "category" => "entertainment"),
            "Colors" => array("url" => "https://colors.akamaized.net/hls/live/2018075/colors/master.m3u8", "category" => "entertainment"),
            "Star Plus" => array("url" => "https://starplus.akamaized.net/hls/live/2018076/starplus/master.m3u8", "category" => "entertainment"),
            "Zee TV" => array("url" => "https://zeetv.akamaized.net/hls/live/2018074/zeetv/master.m3u8", "category" => "entertainment"),
            "Star Bharat" => array("url" => "https://starbharat.akamaized.net/hls/live/2018089/starbharat/master.m3u8", "category" => "entertainment"),
            "Sony TV" => array("url" => "https://sonytv.akamaized.net/hls/live/2018090/sonytv/master.m3u8", "category" => "entertainment"),
            "Colors Rishtey" => array("url" => "https://colorsrishtey.akamaized.net/hls/live/2018091/colorsrishtey/master.m3u8", "category" => "entertainment"),
            "Zee Anmol" => array("url" => "https://zeeanmol.akamaized.net/hls/live/2018092/zeeanmol/master.m3u8", "category" => "entertainment"),
            "Star Utsav" => array("url" => "https://starutsav.akamaized.net/hls/live/2018093/starutsav/master.m3u8", "category" => "entertainment"),

            // Movies Channels
            "Sony Max" => array("url" => "https://sonymax.akamaized.net/hls/live/2018079/sonymax/master.m3u8", "category" => "movies"),
            "Star Gold" => array("url" => "https://stargold.akamaized.net/hls/live/2018080/stargold/master.m3u8", "category" => "movies"),
            "Zee Cinema" => array("url" => "https://zeecinema.akamaized.net/hls/live/2018081/zeecinema/master.m3u8", "category" => "movies"),
            "Movies OK" => array("url" => "https://moviesok.akamaized.net/hls/live/2018082/moviesok/master.m3u8", "category" => "movies"),
            "Star Gold Select" => array("url" => "https://stargoldselect.akamaized.net/hls/live/2018094/stargoldselect/master.m3u8", "category" => "movies"),
            "Sony Max 2" => array("url" => "https://sonymax2.akamaized.net/hls/live/2018095/sonymax2/master.m3u8", "category" => "movies"),
            "Zee Action" => array("url" => "https://zeeaction.akamaized.net/hls/live/2018096/zeeaction/master.m3u8", "category" => "movies"),
            "Star Gold 2" => array("url" => "https://stargold2.akamaized.net/hls/live/2018097/stargold2/master.m3u8", "category" => "movies"),
            "Zee Classic" => array("url" => "https://zeeclassic.akamaized.net/hls/live/2018098/zeeclassic/master.m3u8", "category" => "movies"),

            // Music Channels
            "MTV" => array("url" => "https://mtv.akamaized.net/hls/live/2018083/mtv/master.m3u8", "category" => "music"),
            "9XM" => array("url" => "https://9xm.akamaized.net/hls/live/2018084/9xm/master.m3u8", "category" => "music"),
            "B4U Music" => array("url" => "https://b4umusic.akamaized.net/hls/live/2018085/b4umusic/master.m3u8", "category" => "music"),
            "MTV Beats" => array("url" => "https://mtvbeats.akamaized.net/hls/live/2018099/mtvbeats/master.m3u8", "category" => "music"),
            "9X Jalwa" => array("url" => "https://9xjalwa.akamaized.net/hls/live/2018100/9xjalwa/master.m3u8", "category" => "music"),
            "B4U Movies" => array("url" => "https://b4umovies.akamaized.net/hls/live/2018101/b4umovies/master.m3u8", "category" => "music"),
            "Mastiii" => array("url" => "https://mastiii.akamaized.net/hls/live/2018102/mastiii/master.m3u8", "category" => "music"),
            "Zing" => array("url" => "https://zing.akamaized.net/hls/live/2018103/zing/master.m3u8", "category" => "music"),

            // Sports Channels
            "Star Sports 1" => array("url" => "https://starsports1.akamaized.net/hls/live/2018086/starsports1/master.m3u8", "category" => "sports"),
            "Sony Ten 1" => array("url" => "https://sonyten1.akamaized.net/hls/live/2018087/sonyten1/master.m3u8", "category" => "sports"),
            "DD Sports" => array("url" => "https://ddsports.akamaized.net/hls/live/2018088/ddsports/master.m3u8", "category" => "sports"),
            "Star Sports 2" => array("url" => "https://starsports2.akamaized.net/hls/live/2018104/starsports2/master.m3u8", "category" => "sports"),
            "Sony Ten 2" => array("url" => "https://sonyten2.akamaized.net/hls/live/2018105/sonyten2/master.m3u8", "category" => "sports"),
            "Sony Ten 3" => array("url" => "https://sonyten3.akamaized.net/hls/live/2018106/sonyten3/master.m3u8", "category" => "sports"),
            "Star Sports Select 1" => array("url" => "https://starsportsselect1.akamaized.net/hls/live/2018107/starsportsselect1/master.m3u8", "category" => "sports"),
            "Star Sports Select 2" => array("url" => "https://starsportsselect2.akamaized.net/hls/live/2018108/starsportsselect2/master.m3u8", "category" => "sports"),

            // Regional Channels
            "DD Bangla" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Punjabi" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/20c8ad14-a158-4a42-8889-e032d070856e/2.m3u8", "category" => "regional"),
            "DD Gujarati" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Malayalam" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Telugu" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Kannada" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Oriya" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD Sahyadri" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "DD North East" => array("url" => "https://d3eyhgoylams0m.cloudfront.net/v1/manifest/93ce20f0f52760bf38be911ff4c91ed02aa2fd92/ed7bd2c7-8d10-4051-b397-2f6b90f99acb/245d9a9e-4820-43b1-af33-4a3017d09f52/2.m3u8", "category" => "regional"),
            "Sun TV" => array("url" => "https://suntv.akamaized.net/hls/live/2018109/suntv/master.m3u8", "category" => "regional"),
            "Gemini TV" => array("url" => "https://geminitv.akamaized.net/hls/live/2018110/geminitv/master.m3u8", "category" => "regional"),
            "Zee Tamil" => array("url" => "https://zeetamil.akamaized.net/hls/live/2018111/zeetamil/master.m3u8", "category" => "regional"),
            "Star Maa" => array("url" => "https://starmaa.akamaized.net/hls/live/2018112/starmaa/master.m3u8", "category" => "regional"),
            "Zee Marathi" => array("url" => "https://zeemarathi.akamaized.net/hls/live/2018113/zeemarathi/master.m3u8", "category" => "regional"),
            "Star Pravah" => array("url" => "https://starpravah.akamaized.net/hls/live/2018114/starpravah/master.m3u8", "category" => "regional"),
            "Zee Bangla" => array("url" => "https://zeebangla.akamaized.net/hls/live/2018115/zeebangla/master.m3u8", "category" => "regional"),
            "Star Jalsha" => array("url" => "https://starjalsha.akamaized.net/hls/live/2018116/starjalsha/master.m3u8", "category" => "regional"),
            "Zee Kannada" => array("url" => "https://zeekannada.akamaized.net/hls/live/2018117/zeekannada/master.m3u8", "category" => "regional"),
            "Star Suvarna" => array("url" => "https://starsuvarna.akamaized.net/hls/live/2018118/starsuvarna/master.m3u8", "category" => "regional"),
            "Zee Telugu" => array("url" => "https://zeetelugu.akamaized.net/hls/live/2018119/zeetelugu/master.m3u8", "category" => "regional"),
            "Star Vijay" => array("url" => "https://starvijay.akamaized.net/hls/live/2018120/starvijay/master.m3u8", "category" => "regional"),
            "Zee Malayalam" => array("url" => "https://zeemalayalam.akamaized.net/hls/live/2018121/zeemalayalam/master.m3u8", "category" => "regional"),
            "Asianet" => array("url" => "https://asianet.akamaized.net/hls/live/2018122/asianet/master.m3u8", "category" => "regional"),
            "Zee Punjabi" => array("url" => "https://zeepunjabi.akamaized.net/hls/live/2018123/zeepunjabi/master.m3u8", "category" => "regional"),
            "PTC Punjabi" => array("url" => "https://ptc.akamaized.net/hls/live/2018124/ptc/master.m3u8", "category" => "regional"),
            "Zee 24 Taas" => array("url" => "https://zee24taas.akamaized.net/hls/live/2018125/zee24taas/master.m3u8", "category" => "regional"),
            "ABP Majha" => array("url" => "https://abpmajha.akamaized.net/hls/live/2018126/abpmajha/master.m3u8", "category" => "regional"),
            "Zee 24 Kalak" => array("url" => "https://zee24kalak.akamaized.net/hls/live/2018127/zee24kalak/master.m3u8", "category" => "regional"),
            "ABP Ananda" => array("url" => "https://abpananda.akamaized.net/hls/live/2018128/abpananda/master.m3u8", "category" => "regional"),
            "Zee 24 Ghanta" => array("url" => "https://zee24ghanta.akamaized.net/hls/live/2018129/zee24ghanta/master.m3u8", "category" => "regional"),
            "ABP Asmita" => array("url" => "https://abpasmita.akamaized.net/hls/live/2018130/abpasmita/master.m3u8", "category" => "regional"),
            "Zee 24 Ghantalu" => array("url" => "https://zee24ghantalu.akamaized.net/hls/live/2018131/zee24ghantalu/master.m3u8", "category" => "regional"),
            "ABP Sanjha" => array("url" => "https://abpsanjha.akamaized.net/hls/live/2018132/abpsanjha/master.m3u8", "category" => "regional")
        );

        foreach ($channels as $channelName => $channelData) {
          $safeChannelName = htmlspecialchars($channelName, ENT_QUOTES, 'UTF-8');
          $safeUrl = htmlspecialchars($channelData['url'], ENT_QUOTES, 'UTF-8');
          $safeCategory = htmlspecialchars($channelData['category'], ENT_QUOTES, 'UTF-8');
          
          // Generate color based on category
          $categoryColors = [
            'news' => 'from-red-600 to-red-800',
            'entertainment' => 'from-purple-600 to-purple-800',
            'movies' => 'from-yellow-600 to-yellow-800',
            'music' => 'from-pink-600 to-pink-800',
            'sports' => 'from-green-600 to-green-800',
            'regional' => 'from-blue-600 to-blue-800'
          ];
          $gradientColor = isset($categoryColors[$safeCategory]) ? $categoryColors[$safeCategory] : 'from-gray-600 to-gray-800';
          
          echo '<div class="channel-card bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300" data-category="' . $safeCategory . '">';
          echo '<div class="relative bg-gradient-to-br ' . $gradientColor . ' h-48 flex items-center justify-center group cursor-pointer" onclick="playChannel(\'' . $safeUrl . '\', \'' . $safeChannelName . '\')">';
          echo '<div class="text-center">';
          echo '<i class="fas fa-play-circle text-white text-6xl mb-3 group-hover:scale-110 transition-transform duration-300"></i>';
          echo '<h3 class="text-white text-xl font-bold px-4">' . $safeChannelName . '</h3>';
          echo '</div>';
          echo '<div class="absolute top-2 right-2 bg-black bg-opacity-50 px-2 py-1 rounded text-xs text-white uppercase">' . $safeCategory . '</div>';
          echo '</div>';
          echo '<div class="p-4">';
          echo '<button onclick="playChannel(\'' . $safeUrl . '\', \'' . $safeChannelName . '\')" 
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition-colors duration-200">
                  <i class="fas fa-play"></i>
                  <span>Watch Now</span>
                </button>';
          echo '</div></div>';
        }
        ?>
      </div>
    </main>
  </div>

  <script>
    const channels = <?php echo json_encode($channels); ?>;
    const channelKeys = Object.keys(channels);
    let currentIndex = -1;
    let currentHls = null;

    // Play Channel Function
    function playChannel(channelUrl, channelName) {
      if (!channelUrl) {
        alert(`${channelName}: Channel link not available.`);
        return;
      }

      const sanitizedChannelName = channelName.replace(/[<>'"]/g, '');
      const video = document.getElementById('videoPlayerElement');

      // Destroy existing HLS instance
      if (currentHls) {
        currentHls.destroy();
        currentHls = null;
      }

      // Reset video element
      video.pause();
      video.removeAttribute('src');
      video.load();

      // Check HLS support
      if (!Hls.isSupported()) {
        alert('HLS is not supported in your browser. Please use a modern browser.');
        return;
      }

      // Create new HLS instance
      const hls = new Hls({
        enableWorker: true,
        lowLatencyMode: true,
        backBufferLength: 90
      });
      currentHls = hls;

      hls.on(Hls.Events.ERROR, function (event, data) {
        if (data.fatal) {
          switch(data.type) {
            case Hls.ErrorTypes.NETWORK_ERROR:
              console.error('Network error, attempting to recover...');
              hls.startLoad();
              break;
            case Hls.ErrorTypes.MEDIA_ERROR:
              console.error('Media error, attempting to recover...');
              hls.recoverMediaError();
              break;
            default:
              console.error('Fatal error, destroying HLS instance');
              hls.destroy();
              currentHls = null;
              alert('Unable to load channel. Please try another channel.');
              break;
          }
        }
      });

      hls.loadSource(channelUrl);
      hls.attachMedia(video);

      hls.on(Hls.Events.MANIFEST_PARSED, function() {
        video.play().catch(e => {
          console.error('Error playing video:', e);
          alert('Unable to play video. Please check your connection.');
        });
        $('#videoSection').removeClass('hidden');
        $('#channelGrid').addClass('hidden');
      });

      document.getElementById('currentChannelName').textContent = sanitizedChannelName;
      currentIndex = channelKeys.indexOf(channelName);
      addToRecent(sanitizedChannelName);
    }

    // Navigate Channels
    function navigateChannel(direction) {
      if (currentIndex === -1) return;
      currentIndex += direction;
      if (currentIndex < 0) currentIndex = channelKeys.length - 1;
      if (currentIndex >= channelKeys.length) currentIndex = 0;

      const nextChannelName = channelKeys[currentIndex];
      const nextChannelUrl = channels[nextChannelName].url;
      playChannel(nextChannelUrl, nextChannelName);
    }

    // Search Functionality
    $('#searchInput').on('input', function() {
      const searchTerm = $(this).val().toLowerCase();
      $('.channel-card').each(function() {
        const channelName = $(this).find('h3').text().toLowerCase();
        $(this).toggle(channelName.includes(searchTerm));
      });
    });

    // Category Filtering
    $('.category-btn').click(function() {
      const category = $(this).data('category');
      if (category === 'all') {
        $('.channel-card').show();
      } else {
        $('.channel-card').hide();
        $(`.channel-card[data-category="${category}"]`).show();
      }
    });

    // Event Listeners
    document.getElementById('prevChannel').addEventListener('click', () => navigateChannel(-1));
    document.getElementById('nextChannel').addEventListener('click', () => navigateChannel(1));

    // Favorites Functionality
    let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

    function toggleFavorite(channelName) {
      const index = favorites.indexOf(channelName);
      if (index === -1) {
        favorites.push(channelName);
      } else {
        favorites.splice(index, 1);
      }
      localStorage.setItem('favorites', JSON.stringify(favorites));
    }

    // Recent Channels
    let recentChannels = JSON.parse(localStorage.getItem('recentChannels')) || [];

    function addToRecent(channelName) {
      recentChannels = recentChannels.filter(channel => channel !== channelName);
      recentChannels.unshift(channelName);
      recentChannels = recentChannels.slice(0, 10);
      localStorage.setItem('recentChannels', JSON.stringify(recentChannels));
    }

    // Quality Settings
    function setQuality(quality) {
      const video = document.getElementById('videoPlayerElement');
      if (video) {
        video.quality = quality;
      }
    }

    // Theme Toggle
    function toggleTheme() {
      document.body.classList.toggle('bg-gray-900');
      document.body.classList.toggle('bg-white');
      document.body.classList.toggle('text-white');
      document.body.classList.toggle('text-gray-900');
    }

    // Button Event Listeners
    $('#favoritesBtn').click(function() {
      $('.channel-card').hide();
      favorites.forEach(channelName => {
        $(`.channel-card:contains('${channelName}')`).show();
      });
    });

    $('#recentBtn').click(function() {
      $('.channel-card').hide();
      recentChannels.forEach(channelName => {
        $(`.channel-card:contains('${channelName}')`).show();
      });
    });

    $('#qualityBtn').click(function() {
      const quality = prompt('Enter quality (low, medium, high):');
      if (quality) setQuality(quality);
    });

    $('#themeBtn').click(function() {
      toggleTheme();
    });
  </script>
</body>
</html>

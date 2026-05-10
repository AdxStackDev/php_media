# IPTV Player Application

A comprehensive web-based IPTV streaming application with user authentication, subscription plans, and channel management.

## Features

- **User Authentication**: Secure login system with session management
- **Subscription Plans**: Three-tier plan system (Weekly, Monthly, Yearly)
- **Channel Streaming**: 70+ IPTV channels across multiple categories
- **Category Filtering**: News, Entertainment, Movies, Music, Sports, Regional
- **Search Functionality**: Real-time channel search
- **Favorites System**: Save and manage favorite channels
- **Recently Watched**: Track viewing history
- **Location-Based Regional Channels**: Automatic regional channel suggestions
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **HLS Streaming**: HTTP Live Streaming support with error recovery

## Files Structure

```
├── login.php          # User authentication page
├── plan.php           # Subscription plan selection
├── channel.php        # Main IPTV player interface
├── route.php          # Application routing system
├── logout.php         # Session termination
└── README.md          # Documentation
```

## Installation

1. **Requirements**:
   - PHP 7.4 or higher
   - Web server (Apache/Nginx)
   - Modern web browser with HLS support

2. **Setup**:
   ```bash
   # Clone or download files to your web server directory
   # Example for WAMP:
   C:\wamp64\www\iptv\
   ```

3. **Access**:
   - Navigate to `http://localhost/iptv/login.php`
   - Default credentials: `admin` / `1234`

## Usage Flow

1. **Login** → `login.php`
   - Enter credentials (admin/1234)
   - Session is created upon successful login

2. **Select Plan** → `plan.php`
   - Choose from Plan A (7 days), B (30 days), or C (365 days)
   - Plan details stored in session

3. **Watch Channels** → `channel.php`
   - Browse channels by category
   - Search for specific channels
   - Click "Watch Now" to stream
   - Use Previous/Next buttons to navigate

4. **Logout** → `logout.php`
   - Destroys session and redirects to login

## Fixed Issues

### Security Fixes
1. ✅ **XSS Prevention**: Added `htmlspecialchars()` to all user inputs and outputs
2. ✅ **Session Management**: Implemented proper session handling across all pages
3. ✅ **Access Control**: Added authentication checks on protected pages
4. ✅ **Input Sanitization**: Sanitized channel names and URLs in JavaScript

### Bug Fixes
1. ✅ **Gemini TV URL**: Fixed space in URL (`gemin tv` → `geminitv`)
2. ✅ **Duplicate Channel**: Removed duplicate "Zee 24 Ghantalu" entry
3. ✅ **Missing Font Awesome**: Added Font Awesome CDN for heart icons
4. ✅ **Login Form**: Created proper HTML form with POST handling
5. ✅ **Plan Class**: Made properties accessible and removed echo statements
6. ✅ **Routing**: Fixed route.php to properly handle all pages

### Enhancements
1. ✅ **HLS Configuration**: Added better HLS.js configuration with error handling
2. ✅ **API Rate Limiting**: Added User-Agent header for OpenStreetMap Nominatim
3. ✅ **Error Messages**: Improved user feedback for errors
4. ✅ **UI Improvements**: Enhanced login and plan selection pages with Tailwind CSS
5. ✅ **Code Comments**: Added detailed comments for better maintainability
6. ✅ **Memory Management**: Proper HLS instance cleanup to prevent memory leaks

## Channel Categories

- **News**: 10 channels (Aaj Tak, ABP News, India TV, etc.)
- **Entertainment**: 10 channels (Sony SAB, Colors, Star Plus, etc.)
- **Movies**: 9 channels (Sony Max, Star Gold, Zee Cinema, etc.)
- **Music**: 7 channels (MTV, 9XM, B4U Music, etc.)
- **Sports**: 7 channels (Star Sports, Sony Ten, DD Sports, etc.)
- **Regional**: 30+ channels (DD Bangla, Sun TV, Zee Tamil, etc.)

## Technical Details

### Technologies Used
- **Frontend**: HTML5, CSS3, JavaScript, jQuery, Tailwind CSS
- **Backend**: PHP (Session-based)
- **Video**: HLS.js, Video.js
- **Icons**: Font Awesome 6.4.0
- **Storage**: LocalStorage for favorites and recent channels

### Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Known Limitations
1. **Stream URLs**: Some channel URLs may be inactive or require authentication
2. **CORS**: Some streams may have CORS restrictions
3. **Geolocation**: OpenStreetMap Nominatim has usage limits (consider paid alternatives)
4. **No Database**: User data and plans are session-based (not persistent)
5. **Hardcoded Credentials**: For demo purposes only

## Security Recommendations for Production

1. **Database Integration**: Store users, plans, and subscriptions in a database
2. **Password Hashing**: Use `password_hash()` and `password_verify()`
3. **HTTPS**: Enable SSL/TLS for secure connections
4. **CSRF Protection**: Implement CSRF tokens for forms
5. **Rate Limiting**: Add rate limiting for login attempts
6. **Payment Gateway**: Integrate real payment processing
7. **Stream Authentication**: Implement token-based stream access
8. **Environment Variables**: Move sensitive data to .env files

## Future Enhancements

- [ ] User registration system
- [ ] Payment gateway integration
- [ ] Database persistence
- [ ] Admin panel for channel management
- [ ] Video quality selection
- [ ] Subtitle support
- [ ] Chromecast support
- [ ] Mobile app (React Native/Flutter)
- [ ] EPG (Electronic Program Guide)
- [ ] Parental controls

## Troubleshooting

### Video Won't Play
- Check browser console for errors
- Verify stream URL is accessible
- Ensure HLS.js is loaded properly
- Try a different channel

### Login Issues
- Clear browser cookies and cache
- Check PHP session configuration
- Verify file permissions

### Location Not Working
- Enable browser location permissions
- Check internet connectivity
- OpenStreetMap API may be rate-limited

## License

This is a demonstration project. Channel URLs and content are property of their respective owners.

## Support

For issues or questions, please check the browser console for error messages and verify all dependencies are loaded correctly.

---

**Last Updated**: 2026-05-11
**Version**: 1.0.0

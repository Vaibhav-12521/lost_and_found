# 🎯 Lost & Found Application

A complete web application for reporting and finding lost items with user authentication.

## 🚀 Features

- ✅ User Registration & Login
- ✅ Report Lost Items
- ✅ Report Found Items
- ✅ User Profile Management
- ✅ Search & Filter Items
- ✅ Responsive Design
- ✅ Secure Authentication

## 📋 Requirements

- **XAMPP** (Apache + MySQL + PHP)
- **PHP** 7.0 or higher
- **MySQL** 5.7 or higher
- **Web Browser** (Chrome, Firefox, Safari, Edge)

## 🛠️ Installation

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Install XAMPP on your system
3. Start Apache and MySQL services

### Step 2: Setup Project
1. Copy all project files to: `htdocs/retry3/`
2. Open web browser
3. Go to: `http://localhost/retry3/install.php`
4. Follow the installation wizard

### Step 3: Access Application
- **Main URL**: `http://localhost/retry3/`
- **Sign Up**: `http://localhost/retry3/signup.php`
- **Sign In**: `http://localhost/retry3/signin.php`

## 📁 Project Structure

```
retry3/
├── index.php              # Main homepage
├── signup.php             # User registration
├── signin.php             # User login
├── profile.php            # User profile
├── report_lost.php        # Report lost items
├── report_found.php       # Report found items
├── logout.php             # Logout functionality
├── config.php             # Database configuration
├── setup_database.php     # Database setup
├── install.php            # Installation script
└── README.md              # This file
```

## 🔧 Configuration

### Database Settings (config.php)
```php
$host = "localhost";
$username = "root";
$password = "";
$database = "lost_found";
```

### Custom Database
If you want to use a different database:
1. Edit `config.php`
2. Change database name, username, password
3. Run `setup_database.php` again

## 🎯 Usage

### For Users:
1. **Sign Up**: Create a new account
2. **Sign In**: Login to your account
3. **Report Lost**: Report your lost items
4. **Report Found**: Report found items
5. **Search**: Find lost/found items
6. **Profile**: Manage your account

### For Administrators:
- Access user management
- Monitor reports
- Manage categories

## 🔒 Security Features

- ✅ Password hashing
- ✅ SQL injection protection
- ✅ Session management
- ✅ Input validation
- ✅ XSS protection

## 🐛 Troubleshooting

### Common Issues:

**1. Database Connection Error**
- Check if MySQL is running
- Verify database credentials in `config.php`

**2. Page Not Found**
- Ensure Apache is running
- Check file permissions
- Verify file paths

**3. Port Issues**
- Change Apache port if 80 is busy
- Use: `http://localhost:8080/retry3/`

**4. Permission Issues (Linux/Mac)**
```bash
chmod 755 /opt/lampp/htdocs/retry3/
chmod 644 /opt/lampp/htdocs/retry3/*.php
```

## 📞 Support

If you encounter any issues:
1. Check XAMPP services are running
2. Verify database connection
3. Check error logs in XAMPP
4. Ensure all files are in correct location

## 🎉 Success!

Once installed, you can:
- ✅ Register new users
- ✅ Login with existing accounts
- ✅ Report lost and found items
- ✅ Search through reports
- ✅ Manage user profiles

---

**Made with ❤️ for helping people find their lost belongings** 
# QuizMaker

A dynamic online quiz application built with PHP, MySQL, HTML, CSS, and JavaScript. QuizMaker allows users to create quizzes, manage questions, take quizzes, and view results.

## Features

- Create, edit, and delete quizzes.
- Store quizzes and questions in a MySQL database.
- Let participants take quizzes and submit answers.
- Record and view quiz results.
- Share quizzes through links, if enabled in your version.
- User accounts and login, if configured.
- Optional AI quiz generation using the Google Gemini API.
- Optional email verification and password-reset messages using the Brevo API.

> Some features depend on the PHP files, database schema, and API integrations included in your copy of the project.

## Technologies Used

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL or MariaDB
- **Local development:** XAMPP
- **Web hosting:** InfinityFree or another host that supports PHP and MySQL
- **AI integration:** Google Gemini API (optional)
- **Email integration:** Brevo API (optional)

## Requirements

Before installing QuizMaker, prepare:

- A computer with Windows, macOS, or Linux.
- PHP and MySQL/MariaDB.
- XAMPP for local development, or PHP-compatible web hosting.
- A web browser.
- The QuizMaker project files.
- The database SQL file supplied with your project.
- API keys only if you want to enable the optional AI or email features.

## 1. Download the Project

Clone the repository:

```bash
git clone https://github.com/iwantocode2/quizmaker.git
```

Alternatively, open the repository on GitHub and select **Code → Download ZIP**.

Extract the downloaded ZIP file.

## 2. Run QuizMaker Locally Using XAMPP

1. Install and open XAMPP.
2. Start **Apache** and **MySQL**.
3. Copy the QuizMaker project folder into the XAMPP `htdocs` directory.

Example location:

```text
C:\xampp\htdocs\quizmaker\
```

4. Open phpMyAdmin:

   http://localhost/phpmyadmin

5. Create a database for QuizMaker. For example:

```text
quizmaker
```

6. Select the new database and import the SQL schema file supplied with the project.
7. Configure the database connection in `connect.php`.
8. Open the application:

   http://localhost/quizmaker/

## 3. Configure the Database Connection

QuizMaker uses PHP to connect to MySQL. The connection settings must match the database server where the application is installed.

For a default local XAMPP installation, a connection may look like this:

```php
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "quizmaker";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>
```

**Important:** This is an example for local XAMPP. Use the actual settings supplied by your hosting provider for a live website. Do not assume the online database username, password, hostname, or database name is the same as your local configuration.

Never upload real database credentials to a public GitHub repository.

## 4. Database Setup

The database stores the application's information, such as quizzes, questions, and quiz attempts.

Depending on the version of the project, tables may include:

- `quizzes`
- `questions`
- `quiz_attempts`
- User or authentication tables, if implemented.

Import the SQL schema included in the repository.

If an upgrade SQL file is provided, use it only when its instructions match your existing database version. Do not import an upgrade script into a live database without understanding its changes and keeping a backup.

After importing, check that the tables and columns match the PHP files in your version of QuizMaker.

## 5. Optional Google Gemini API Integration

The Gemini API can be used for AI-powered quiz generation if the feature is implemented in your project.

### Get an API key

1. Open [Google AI Studio](https://aistudio.google.com/).
2. Sign in with your Google account.
3. Open the API key section.
4. Create an API key for your project.
5. Store the key privately.

### Configure the application

Set the API key in a private server-side configuration location supported by your application.

Example environment variable:

```text
GEMINI_API_KEY=your_own_gemini_api_key
```

This is a placeholder, not a working key. The existing PHP code must explicitly read this environment variable, or use the private configuration method implemented by your project.

**Security requirements:**

- Never put the Gemini API key in `script.js`, HTML, or other browser-accessible code.
- Never commit a real key to GitHub.
- Handle API errors and rate limits in the backend.
- Check current Gemini API terms, quotas, model availability, and pricing before deployment.

Official documentation: [Gemini API documentation](https://ai.google.dev/gemini-api/docs)

## 6. Optional Brevo Email API Integration

Brevo can send transactional emails, such as account verification codes or password-reset OTPs, if those features are implemented in your copy.

### Configure Brevo

1. Create or sign in to your account at [Brevo](https://www.brevo.com/).
2. Open the API keys section.
3. Generate an API key.
4. Configure an authorized sender address in Brevo.
5. Store the API key privately on the server.
6. Configure the sender address and email settings in the PHP integration.

Example configuration values:

```text
BREVO_API_KEY=your_own_brevo_api_key
BREVO_SENDER_EMAIL=your_verified_sender@example.com
```

These are examples only. Replace them with your own credentials and verified sender details. The PHP application must be programmed to read these settings.

**Security requirements:**

- Keep the API key out of frontend JavaScript and HTML.
- Do not publish API keys in GitHub.
- Validate email addresses and OTP submissions on the server.
- Apply expiration and attempt limits to verification codes.
- Handle failed email requests safely.

Official documentation: [Brevo API documentation](https://developers.brevo.com/)

## 7. Deploy to InfinityFree

To publish QuizMaker online, use a PHP-compatible hosting service such as InfinityFree.

### Upload the website

1. Sign in to your hosting account.
2. Open the hosting control panel.
3. Open File Manager.
4. Enter the website's `htdocs` directory.
5. Upload the QuizMaker files and folders.
6. Confirm that `index.html` or the appropriate entry page is in the correct location.
7. Keep the existing PHP files and assets required by the application.

### Create the online database

1. Open the MySQL database management section in your hosting panel.
2. Create a database.
3. Record the database hostname, full database name, username, and password shown by your host.
4. Open phpMyAdmin for that database.
5. Import the project's database schema.
6. Update the server-side connection settings in `connect.php` to use the online database credentials.

Do not use the default local XAMPP credentials on the live website.

### Configure the domain and HTTPS

1. Connect your domain or hosting-provided subdomain.
2. Follow your host's DNS instructions.
3. Enable SSL/HTTPS using the options supported by your hosting provider.
4. Wait for certificate issuance and DNS changes to complete.
5. Open the HTTPS version of your website and test it.

Example project website:

https://quizmakeronline.site.je

The URL is an example of the project's previously reported hosting address. Its current availability and SSL status should be verified before relying on it.

### Test the live website

Check that:

- The homepage and CSS load correctly.
- PHP endpoints respond as expected.
- The database connection works.
- Quizzes can be created and retrieved.
- Participants can submit answers.
- Results are saved correctly.
- Login, email, and AI features work if enabled.
- Shared quizzes open as intended.

## 8. Security and Privacy

Before making a public deployment:

- Keep passwords and API keys private.
- Use prepared SQL statements for database queries.
- Validate and sanitize user input.
- Use secure password hashing for accounts.
- Protect authenticated pages and APIs with server-side authorization checks.
- Verify that one account cannot access another account's private quizzes or results unless explicitly permitted.
- Use HTTPS for public access.
- Do not expose database errors or credentials to visitors.
- Disable or remove temporary setup and debugging scripts from the public website.
- Do not upload database backups containing personal information.
- Back up the database before applying schema changes.

A separate login page does not by itself guarantee account isolation. Verify authorization throughout the backend before allowing multiple users to store private data.

## 9. Troubleshooting

### Database connection failed

- Check the database hostname, name, username, and password.
- Confirm that the database exists.
- Confirm that the hosting account permits the connection.
- Check that the imported schema matches the application's PHP files.

### Could not load quizzes

- Confirm that the database contains the expected quiz tables and columns.
- Check the browser's Network tab and the hosting PHP error log.
- Verify the response from the quiz API endpoint.

### CSS is missing

- Check that `style.css` is uploaded.
- Confirm that the path in the HTML file is correct.
- Open the CSS file URL directly in the browser.
- Check for 404 errors in the browser's Network tab.

### AI generation fails

- Check the server-side API configuration.
- Verify the API key, model name, quota, and API request format.
- Review server-side logs without exposing the API key.

### Email delivery fails

- Verify the API key and sender configuration.
- Check the provider's transactional email logs.
- Confirm that the PHP request and error handling are correct.

## 10. Contributing

Contributions and improvements are welcome.

1. Fork the repository.
2. Create a branch for your changes.
3. Make and test your changes locally.
4. Submit a pull request describing the changes.

Do not include real credentials, private user data, or production database dumps in commits.

## License

Add a `LICENSE` file before distributing the project under a specific open-source license. Until a license is included, do not assume that others have permission to reuse or redistribute the code.

---

**Repository:** https://github.com/iwantocode2/quizmaker

**Project type:** Dynamic web application

**Backend:** PHP

**Database:** MySQL/MariaDB

**Optional integrations:** Google Gemini API and Brevo API

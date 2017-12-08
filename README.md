<div align="center">
	<h1> REST Api with Slim PHP </h1>
</div>

<div align="center">
	<a href="#changelog">
		<img src="https://img.shields.io/badge/stability-stable-green.svg" alt="Status">
	</a>
	<a href="#changelog">
		<img src="https://img.shields.io/badge/release-v1.0.0.1-blue.svg" alt="Version">
	</a>
	<a href="#changelog">
		<img src="https://img.shields.io/badge/update-december-yellowgreen.svg" alt="Update">
	</a>
	<a href="#license">
		<img src="https://img.shields.io/badge/license-MIT%20License-green.svg" alt="License">
	</a>
</div>

This API works with the same concept of social network of [Fav Quote](https://github.com/ManuelGil/Simple-Social-Network).

This is a simple REST Web Service which allow:

  * Post short text messages of no more than 120 characters
  * Bring a list with the latest published messages
  * Search for messages by your text
  * Delete a specific message by its id

<a name="started"></a>
## :traffic_light: Getting Started

This page will help you get started with this API.

<a name="requirements"></a>
### Requirements

  * PHP 5.6
  * MySQL or MariaDB
  * Apache Server

<a name="installation"></a>
### Installation

#### Create a database

Run the following SQL script

```SQL
-- -----------------------------------------------------
-- Schema NETWORK
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `NETWORK` DEFAULT CHARACTER SET utf8 ;
USE `NETWORK` ;

-- -----------------------------------------------------
-- Table `NETWORK`.`USERS`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `NETWORK`.`USERS` (
  `ID_USER` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `USERNAME` VARCHAR(20) NOT NULL,
  `PASSWORD` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ID_USER`),
  UNIQUE INDEX `ID_USER_UNIQUE` (`ID_USER` ASC),
  UNIQUE INDEX `USER_UNIQUE` (`USERNAME` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `NETWORK`.`QUOTES`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `NETWORK`.`QUOTES` (
  `ID_QUOTE` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `QUOTE` VARCHAR(120) NOT NULL,
  `POST_DATE` DATETIME NOT NULL DEFAULT NOW(),
  `LIKES` INT UNSIGNED NOT NULL DEFAULT 0,
  `ID_USER` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`ID_QUOTE`),
  UNIQUE INDEX `ID_QUOTE_UNIQUE` (`ID_QUOTE` ASC),
  INDEX `fk_QUOTES_USERS_idx` (`ID_USER` ASC),
  CONSTRAINT `fk_QUOTES_USERS`
    FOREIGN KEY (`ID_USER`)
    REFERENCES `NETWORK`.`USERS` (`ID_USER`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
```

#### Create a Slim project

  1. Go to htdocs dir

    * Windows

    ```bash
    $ cd /d C:\xampp\htdocs
    ```

    * Linux

    ```bash
    $ cd /opt/lampp/htdocs
    ```

    * MAC

    ```bash
    $ cd applications/mamp/htdocs
    ```

  2. Creates a new folder

    ```bash
    $ mkdir rest
    ```

  3. Go to new folder
  
    ```bash
    $ cd rest
    ```

  4. Install Slim Framework 3

    ```bash
    $ composer require slim/slim "^3.0"  
    ```

  5. Install JWT Authentication Middleware

    ```bash
    $ composer require tuupola/slim-jwt-auth
    ```

#### Copy this project

  1. Clone or Download this repository
  2. Unzip the archive if needed
  3. Copy the folder in the Slim project dir
  4. Start a Text Editor (Atom, Sublime, Visual Studio Code, Vim, etc)
  5. Add the project folder to the editor

<a name="built"></a>
## :wrench: Built With

  * XAMPP ([XAMPP for Windows 5.6.32](https://www.apachefriends.org/download.html))
  * ATOM ([ATOM](https://atom.io/))
  * RestEasy Extension for Chrome ([RestEasy](https://chrome.google.com/webstore/detail/resteasy/nojelkgnnpdmhpankkiikipkmhgafoch))

<a name="test"></a>
## :100: Running the tests

Use RestEasy or Postman app for testing.

For authentication you can generate a new JSON Web Token with the url login.

Put the token on an HTTP header called Authorization. e.g.:

  * Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ

<div align="center">
	<img src="https://github.com/ManuelGil/REST-Api-with-Slim-PHP/blob/master/docs/screenshots/header.png?raw=true" alt="Header">
</div>

Put the parameters on a Query Parameter.

<div align="center">
	<img src="https://github.com/ManuelGil/REST-Api-with-Slim-PHP/blob/master/docs/screenshots/test.png?raw=true" alt="Test">
</div>

<a name="changelog"></a>
## :information_source: Changelog

**1.0.0.1** (12/07/2017)

  * <table border="0" cellpadding="4">
		<tr>
			<td>
				<strong>Language:</strong>
			</td>
			<td>
				PHP
			</td>
		</tr>
		<tr>
			<td><strong>
				Requirements:
			</strong></td>
			<td>
				<ul>
					<li>
						PHP 5.6
					</li>
					<li>
						MySQL or MariaDB 
					</li>
					<li>
						Apache Server
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Changes:</strong>
			</td>
			<td>
				<ul>
					<li>
						Add Authentication with <a href="https://github.com/tuupola/slim-jwt-auth">PSR-7 JWT Authentication Middleware</a>
					</li>
				</ul>
			</td>
		</tr>
	</table>

<a name="Donate"></a>
## :gift: Donate!

If you want to help me to continue this project, you might donate via PayPal.

<a href="https://paypal.me/ManuelFGil"><img src="https://www.paypalobjects.com/webstatic/en_US/i/btn/png/btn_donate_92x26.png" alt="Donate via PayPal"></a>

<a name="authors"></a>
## :eyeglasses: Authors

  * **Manuel Gil** - *Initial work* - [ManuelGil](https://github.com/ManuelGil) 

See also the list of [contributors](https://github.com/ManuelGil/REST-Api-with-Slim-PHP/contributors)
 who participated in this project.

<a name="license"></a>
## :memo: License

This API is licensed under the MIT License - see the
 [MIT License](https://opensource.org/licenses/MIT) for details.

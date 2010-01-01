<?php
// START USER CONFIG VALUES	==============================================================================================
// Uncomment the following line to choose a custom config file path.
// define('CONFIGPATH', '/etc/coreman/core-config.php');
// Uncomment the following line to choose a custom name.
define('NAME', 'EduSquared');
// Uncomment the following line to choose a custom repository.
define('REPO', 'http://edusquared.org/trunk/');
// END USER CONFIG VALUES	==============================================================================================

// START DEFAULT DEFINITIONS
if (!defined('CONFIGPATH'))
{
	define('CONFIGPATH', 'core-config.php');
}

if (!defined('NAME'))
{
	define('NAME', 'Coreman');
}

if (!defined('REPO'))
{
	define('REPO', 'http://phpcoreman.com/trunk/');
}
//END DEFAULT DEFINITIONS

// START DISPLAY CONSTANTS	==============================================================================================
// DEFAULT VALUES
$values = array();
$values['name'] = NAME;
$values['lname'] = strtolower(NAME);
$values['url'] = $_SERVER['PHP_SELF'];
$values['message'] = '';
$values['error'] = '';
// ENGLISH TRANSLATIONS
$strings = array();
$strings['ourusername'] = '!name! Username';
$strings['ourpassword'] = '!name! Password';
$strings['mysqlusername'] = 'MySQL Username';
$strings['mysqlpassword'] = 'MySQL Password';
$strings['mysqlhost'] = 'MySQL Host';
$strings['mysqldatabase'] = 'MySQL Database';
$strings['mysqlprefix'] = 'MySQL Prefix';
$strings['submit'] = 'Submit';
$strings['error'] = 'We have encountered an error';
$strings['fatalerror'] = 'We have encountered a fatal error';
$strings['welcome'] = 'Welcome to !name!';
$strings['dostuff'] = 'Please do some stuff';
$strings['authfail'] = 'Your username or password is incorrect';
$strings['dbfail'] = 'A connection to the database could not be established';
$strings['tooshort'] = 'Please enter a password at least 8 characters in length';
$strings['close'] = 'Almost finished';
$strings['create'] = 'Please create the file !filename! containing the content below';
$strings['success'] = 'Success';
$strings['created'] = 'Your configuration file has been sucessfully created';
$strings['nologin'] = 'Please enter a username and password';
$strings['setup'] = 'Setup';
$strings['login'] = 'Login';
$strings['filefail'] = 'Could not write file';
$strings['dbwritefail'] = 'Data could not be written to the database';
// TEMPLATES
$templates = array();
$templates['header'] = '<html><head><title>!name!</title><script>^script^</script></head><body style="margin:0;"><div style="background-color:#44c;font-weight:900;font-size:50px;padding:0 15px;margin:0px;"><span style="color:#fff;">E</span><span style="color:#eee;">d</span><span style="color:#ddd;">u</span><sup><span style="color:#ccc;">2</span></sup></div>';
$templates['script'] = '';
$templates['footer'] = '</body></html>';
$templates['setup'] = '<h1>$setup$</h1><div id="message">!message!</div><form name="input" action="!url!" method="post">$ourusername$:<input type="text" name="username" value="admin"/><br />$ourpassword$:<input type="password" name="password" /><br />$ourpassword$:<input type="password" name="password2" /><br />$mysqlusername$:<input type="text" name="db_username" value="!lname!"/><br />$mysqlpassword$:<input type="password" name="db_password" /><br />$mysqlhost$:<input type="text" name="db_host" value="localhost"/><br />$mysqldatabase$:<input type="text" name="db_name" value="!lname!"/><br />$mysqlprefix$:<input type="text" name="db_name" value="!lname!"/><br /><input type="hidden" name="action" value="setup-submit"/><input type="submit" value="$submit$" /><br /></form>';
$templates['fatalerror']='<h1 style="color:red;">$fatalerror$: !error!!</h1>';
$templates['error']='<span id="error" style="color:red;">$error$: !error!!</span>';
$templates['welcome'] = '<h1>$welcome$</h1><p>$dostuff$!</p>';
$templates['writethis'] = '<h1>$close$</h1><p>$create$.</p><textarea readonly="readonly">!file!</textarea>';
$templates['successwrite'] = '<h1>$success$</h1><p>$created$.</p>';
$templates['login'] = '<h1>$login$</h1>!message!<form name="input" action="!url!" method="post">$ourusername$:<input type="text" name="username" value="admin"/><br />$ourpassword$:<input type="text" name="password" /><br /><input type="hidden" name="action" value="login"/><input type="submit" value="$submit$" /><br /></form>';
// END DISPLAY CONSTANTS	==============================================================================================


//START OUTPUT
echo trans('header');

//Open the config file or enter setup.
if (file_exists(CONFIGPATH))
{
	require_once(CONFIGPATH);
	//Verify authentication.
	session_start();
	if (array_key_exists('loggedin', $_SESSION) && $_SESSION['loggedin'])
		//Valid user
		if (is_set($_POST))
		{
			switch ($_POST['action'])
			{
				case 'logout':
					$_SESSION['loggedin'] = false;
					break;
				case 'submit':
					//implement==========================================================================================
					break;
				default:
					echo trans('fatalerror', array('error' => 'Invalid request.'));
			}
		}
		else
		{
			echo trans('welcome', array('name' => NAME));
		}
	else if (isset($_POST) && $_POST['act ion'] == 'login')
	{
		//Check password
		if (array_key_exists($_POST, 'username') && array_key_exists('password') && preg_match('/^[a-zA-Z1-9_-]+$/', $_POST['username']) && $_POST['password']!='')
		{
			if ($con = db_connect())
			{
				$result = mysql_query('SELECT `id` FROM `' . mysql_real_escape_string(MYSQL_PREFIX) . 'core` WHERE `username`="' . mysql_real_escape_string($_POST['username']) . '" AND `password`=SHA1(CONCAT("'.mysql_real_escape_string($_POST['password']).'",hash)) LIMIT 1', $con);
				if (mysql_num_rows($result))
				{
					$_SESSION['loggedin'] = true;
					echo trans('welcome', array('name' => NAME));
				}
				else
				{
					echo trans('fatalerror', array('error' => '$authfail$'));
				}
			}
			else
			{
				echo trans('fatalerror', array('error' => '$dbfail$'));
			}
		}
		else
		{
			echo trans('setup', array('lname' => strtolower(NAME), 'name' => NAME, 'message' => '^error^', 'error' => '$nologin$', 'url' => $_SERVER['PHP_SELF']));
		}
	}
	else
	{
		echo trans('login', array('url' => $_SERVER['PHP_SELF']));
	}
}
else
{
	//Setup
	if (isset($_POST) && array_key_exists('action', $_POST))
	{
		switch ($_POST['action'])
		{
			case 'submit':
				break;
			case 'setup-start':
				echo trans('setup', array('lname' => strtolower(NAME), 'name' => NAME, 'message' => '', 'url' => $_SERVER['PHP_SELF']));
				break;
			case 'setup-submit':
				if (!file_exists(CONFIGPATH))
				{
					if (array_key_exists('db_username', $_POST) && array_key_exists('db_password', $_POST) && array_key_exists('db_name', $_POST) && array_key_exists('username', $_POST) && array_key_exists('password', $_POST) && preg_match('/^[a-zA-Z1-9_-]+$/', $_POST['username']) && preg_match('/^[a-zA-Z1-9_-]+$/', $_POST['username']))
					{
						if (strlen($_POST['password'])>=8)
						{
							//do stuff
							$host = array_key_exists('db_host', $_POST)?$_POST['db_host']:'localhost';
							$prefix = array_key_exists('db_prefix', $_POST)?$_POST['db_prefix']:'';
							if ($con = db_connect($_POST['db_username'], $_POST['db_password'], $_POST['db_name'], $host))
							{
								if (mysql_query('INSERT into `'. $prefix . 'core` (username,password,salt)'))
								{
									$file = "<?php".PHP_EOL."define('MYSQL_HOST','$host');".PHP_EOL."define('MYSQL_USERNAME','$_POST[db_username]');".PHP_EOL."define('MYSQL_PASSWORD','$_POST[db_password]');".PHP_EOL."define('MYSQL_DATABASE','$_POST[db_name]');".PHP_EOL."define('MYSQL_PREFIX','$prefix');".PHP_EOL.'?>';
									if (!is_writable(defined('CONFIGPATH')?dirname(CONFIGPATH):dirname(__file__)))
									{
										echo trans('writethis', array('file' => $file, 'filename' => CONFIGPATH));
									}
									else
									{
										$handle = fopen(CONFIGPATH, 'w+');
										if ($handle)
										{
											fwrite($handle, $file);
											echo trans('successwrite');
										}
										else
										{
											echo trans('fatalerror', array('error' => '$filefail$'));
										}
									}
								}
								else
								echo trans('fatalerror', array('error' => '$dbwritefail$'));
							}
							else
							{
								echo trans('fatalerror', array('error' => '$dbfail$'));
							}
						}
						else
						{
							echo trans('setup', array('lname' => strtolower(NAME), 'name' => NAME, 'url' => $_SERVER['PHP_SELF'], 'message' => '^error^', 'error' => '$tooshort$'));
						}
					}
				}
				break;
			default:
				echo trans('fatalerror', array('error' => 'Invalid request.'));
		}
	}
	else
	{
		echo trans('setup', array('lname' => strtolower(NAME), 'name' => NAME, 'message' => '', 'url' => $_SERVER['PHP_SELF']));
	}
}

echo trans('footer');

function db_connect($user, $password = '', $db = false, $host = 'localhost')
{
	$connection = mysql_connect($host, $user, $password);
	if ($connection && defined($db))
	{
		if (mysql_select_db($db, $connection))
		{
			return $connection;
		}
		else
		{
			return false;
		}
	}
	return $connection;
}

function trans($id, $swaps = null)
{
	global $templates;
	return scan($templates[$id], $swaps);
}

function scan($string, $swaps = null)
{
	global $values, $strings, $templates;
	$swap = false;
	foreach($templates as $search => $replace)
	{
		$string = str_replace('^' . $search . '^', $replace, $string, $count);
		$swap = $swap || $count;
	}
	
	
	
	foreach($strings as $search => $replace)
	{
		$string = str_replace('$' . $search . '$', $replace, $string, $count);
		$swap = $swap || $count;
	}
	
	if(is_array($swaps))
	{
		foreach($swaps as $search => $replace)
		{
			$string = str_replace('!' . $search . '!', $replace, $string, $count);
			$swap = $swap || $count;
		}
	}
	
	if($swap)
	{
		$string = scan($string, $swaps);
	}
	
	foreach($values as $search => $replace)
	{
		$string = str_replace('!' . $search . '!', $replace, $string, $count);
		$swap = $swap || $count;
	}

	return $string;
}
?>
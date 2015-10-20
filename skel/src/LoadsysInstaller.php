<?php
/**
 *
 */
namespace Skel;

use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class LoadsysInstaller
{

    /**
     * Callback method called via composer post the installation of this application.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @return void
     */
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();
        $rootDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
        $fileParser = new FileParser($event, $rootDir);

        static::welcome($io);
        static::parseTemplates($event, $fileParser);
    }

    /**
     * Welcome message called from the postInstall method of this class.
     *
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function welcome($io)
    {
        $io->write('');
        $io->write('<info>Loadsys CakePHP 3 Project Skeleton Installer</info>');
        $io->write('');
        $io->write('The installer is about to scan all *.template files for {{TOKEN}}s');
        $io->write('and prompt you for replacement values.');
        $io->write('');
        $io->write('* Enter a single space " " to remove the token.');
        $io->write('* Leave a prompt blank (no input) to use the indicated default value.');
        $io->write('* If a token does not provide a default value and you leave');
        $io->write('  a prompt blank, the token will not be replaced.)');
        $io->write('');
    }

    /**
     * Finds *.template files and parses tokens strings within each.
     *
     * If a matching local method name is available, the token will be
     * replaced by the value generated by the method. Otherwise, the user
     * will be prompted for a value, and that value will be inserted
     * wherever the token is found in template files.
     *
     * @param Composer\Script\Event $event Composer's Event instance
     * @param FileParser $fileParser A FileParser instance to facilitate scanning and writing template files.
     * @return void
     */
    public static function parseTemplates(Event $event, $fileParser)
    {
        $config = new InstallerConfigurer($event);
        $templates = $fileParser->findTemplates();
        $tokens = $fileParser->getTokensInFiles($templates);

        foreach ($tokens as $token => $default) {
            $replacer = "self::replace{$token}";
            if (is_callable($replacer)) {
                $value = call_user_func($replacer, $default);
            } else {
                $value = $config->prompt($token, $default);
            }
            $config->write($token, $value);
        }

        foreach ($templates as $template) {
            $fileParser->parseTemplate($template, $config->read());
        }
    }

    /**
     * Generate a salt value to inject into config/app.php.
     *
     * By handling the salt value ourselves, we no longer need Cake's
     * default Installer, and can also replace any other tokens we require
     * in the app's config/app.php file.
     *
     * @param string $default Ignored by this method since the salt value is always dynamically generated.
     * @return string A generated salt value.
     * @codeCoverageIgnore Can't test PHP built-in functions.
     */
    public static function replace__SALT__($default) {
        return hash('sha256', __FILE__ . php_uname() . microtime(true));
    }

    /**
     * Generate a vagrant IP address for use in config/provision.yaml.
     *
     * Generating this IP allows for reduced risk of collisions between
     * projects, which allows for multiple VMs to run concurrently without
     * port forwards overlapping.
     *
     * Ideally, at some point this would be partially deterministic based
     * on the "name" of the project. Unfortuantely, that requires knowledge
     * of a human-entered value ({{PROJECT_TITLE}}, currently) that may not
     * have been entered yet depending on the order that tokens are
     * discovered and prompted to the user.
     *
     * @param string $default Ignored by this method since the salt value is always dynamically generated.
     * @return string A generated salt value.
     * @codeCoverageIgnore Can't test a method intended to produce (semi-)random output.
     */
    public static function replace__VAGRANT_IP__($default) {
        $octet3 = abs((int)hash('sha256', __FILE__ . php_uname() . microtime(true)) % 254);
        $octet4 = rand(1, 254);
        return sprintf('172.42.%s.%s', $octet3, $octet4);
    }
}

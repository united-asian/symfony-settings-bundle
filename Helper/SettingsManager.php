<?php

namespace UAM\Bundle\SettingsBundle\DemoBundle\Helper;

use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class SettingsManager
{
    /**
	 * @var string The path to the settings file
	 */
    private $settingsFile;

    /**
	 * @var mixed An array to keep all the loaded settings in.
	 */
    private $settingsArray;

    /**
	 * @var boolean The setters will only work if you set readOnly to false explicitly via the setter.
	 */
    private $readOnly;

    public function __construct($pathToSerializedSettings)
    {
        if (!is_file($pathToSerializedSettings)) {
            throw new \Exception('The settings file could not be found');
        }
        $parser              = new Parser();
        $this->settingsArray = $parser->parse(file_get_contents($pathToSerializedSettings));
        $this->readOnly      = true;
        $this->settingsFile  = $pathToSerializedSettings;
    }

    /**
	 * The function to retrieve a setting. Uses dot notation to address settings. For example:
	 * array('test' => array('someSetting' => VALUE))
	 * To retrieve VALUE you would call get('test.someSetting')
	 *
	 * @param string $name Name of the setting using yaml dot notation
	 * @return mixed|null The setting or null if not found
	 */
    public function get($name)
    {
        $address = explode('.', $name);

        $current = $this->settingsArray;

        foreach ($address as $part) {

            if (isset($current[$part])) {
                $current = $current[$part];
            } else {
                $current = null;
                break;
            }
        }

        return $current;
    }

    /**
	 * @param boolean $read_only
	 */
    public function setReadOnly($read_only)
    {
        $this->readOnly = $ro;
    }

    /**
	 * Set a settings value. Name must be given in yaml dot notation.
	 * If a setting does not exist in the file it will not be created.
	 * This behaviour can be overridden by passing an associative array as the value.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
    public function set($name, $value)
    {
        if ($this->readOnly) {
            throw new \Exception('Settings are read only. Use setReadOnly(false) before altering settings.');
        }

        $address = explode('.', $name);
        $current = &$this->settingsArray;

        foreach ($address as $part) {

            if (isset($current[$part])) {
                $current = &$current[$part];
            } else {
                $current = null;
                break;
            }
        }

        if ($current !== null) {
            $current = $value;
        }
    }

    /**
	 * This function saves the settings in memory to the settings file.
	 * Do all the validation before calling this function.
	 */
    public function saveToFile()
    {
        if ($this->readOnly) {
            throw new \Exception('Settings are read only. Use setReadOnly(false) before altering settings.');
        }

        $dumper = new Dumper();

        $yaml   = $dumper->dump($this->settingsArray, 3);

        file_put_contents($this->settingsFile, $yaml);
    }

    /**
	 * @param string $name The name of the setting to fetch. If null reloads all settings from the file.
	 */
    public function reloadFromFile($name = null)
    {
        $parser = new Parser();

        $originalSettings = $parser->parse(file_get_contents($this->settingsFile));

        if ($name !== null) {
            $address = explode('.', $name);
            $current = $originalSettings;

            foreach ($address as $part) {
                if (isset($current[$part])) {
                    $current = $current[$part];
                } else {
                    $current = null;
                    break;
                }
            }

            $ro             = $this->readOnly;
            $this->readOnly = false;
            $this->set($name, $current);
            $this->readOnly = $ro;
            unset($ro);
        } else {
            $this->settingsArray = $originalSettings;
        }
    }
}

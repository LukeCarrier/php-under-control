<?php
/**
 * This file is part of phpUnderControl.
 *
 * phpUnderControl is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * phpUnderControl is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with phpUnderControl; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * @package phpUnderControl
 */

/**
 * Settings for the php unit tool.
 *
 * @package   phpUnderControl
 * @author    Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright 2007 Manuel Pichler. All rights reserved.
 * @license   GPL http://www.gnu.org/licenses/gpl-3.0.txt
 * @version   $Id$
 * 
 * @property-read boolean $metrics  Enable metrics support?
 * @property-read boolean $coverage Enable coverage support?
 */
class pucPhpUnitSetting extends pucAbstractPearSetting
{   
    /**
     * Minimum code sniffer version.
     */
    const PHP_UNIT_VERSION = '3.2.0RC2';
    
    /**
     * The ctor takes the PEAR install dir as an optional argument.
     * 
     * @param string $pearInstallDir PEAR install dir.
     * @param string $outputDir      An output dir for the generated contents.
     */
    public function __construct( $pearInstallDir = null, $outputDir = null )
    {
        parent::__construct( 'phpunit', $pearInstallDir, $outputDir );
        
        $this->properties['metrics']  = true;
        $this->properties['coverage'] = true;
    }
    
    /**
     * Generates the required output/file content.
     *
     * @return string
     */
    public function generate()
    {
        $metrics = '';
        if ( $this->metrics === true )
        {
            $metrics = '--log-metrics ${basedir}/build/logs/phpunit.metrics.xml';
        }
        $coverage = '';
        if ( $this->coverage === true )
        {
            $coverage = '--coverage-xml ${basedir}/build/logs/phpunit.coverage.xml';
        }
        $output = '';
        if ( $this->outputDir !== null )
        {
            $output = sprintf( '--report %s/coverage', $this->outputDir ); 
        }
        
        $xml = sprintf( '
  <target name="%s">
    <exec executable="%s" dir="${basedir}/source/tests" failonerror="true">
      <arg line="--log-xml ${basedir}/build/logs/phpunit.xml
                 --log-pmd ${basedir}/build/logs/phpunit.pmd.xml
                 %s
                 %s
                 %s
                 PhpUnderControl_Example_MathTest MathTest.php" />
    </exec>
  </target>
',
            $this->cliTool,
            $this->fileName,
            $metrics,
            $coverage,
            $output
        );
        
        return $xml;
    }
    
    /**
     * Validates the existing code sniffer version.
     *
     * @return void
     */
    protected function doValidate()
    {
        ob_start();
        system( "{$this->fileName} --version" );
        $retval = ob_get_contents();
        ob_end_clean();

        if ( preg_match( '/\s+([0-9\.]+(RC[0-9])?)/', $retval, $match ) === 0 )
        {
            echo 'WARNING: Cannot identify PHPUnit version.' . PHP_EOL;
            // Assume valid version
            $version = self::PHP_UNIT_VERSION;
        }
        else
        {
            $version = $match[1];
        }
        
        // Check version and inform user
        if ( version_compare( $version, self::PHP_UNIT_VERSION ) < 0 )
        {
            printf(
                'NOTICE: The identified version %s doesn\'t support metrics.%s' .
                'You may switch to PHPUnit %s for cooler features.%s',
                $version,
                PHP_EOL,
                self::PHP_UNIT_VERSION,
                PHP_EOL
            );
            $this->properties['metrics'] = false;
        }

        // Check xdebug installation
        if ( extension_loaded( 'xdebug' ) === false )
        {
            printf(
                'NOTICE: The xdebug extension is not installed. For coverage%s' .
                'you must install xdebug with the following command:%s' .
                '  pecl install xdebug%s',
                PHP_EOL,
                PHP_EOL,
                PHP_EOL
            );
            $this->properties['coverage'] = false;
        }
    }
}
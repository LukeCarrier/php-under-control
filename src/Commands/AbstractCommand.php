<?php
/**
 * This file is part of phpUnderControl.
 *
 * Copyright (c) 2007, Manuel Pichler <mapi@manuel-pichler.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @package    phpUnderControl
 * @subpackage Commands
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2007 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.phpunit.de/wiki/phpUnderControl
 */

/**
 * Implementation mode of the example mode.
 *
 * @package    phpUnderControl
 * @subpackage Commands
 * @author     Manuel Pichler <mapi@manuel-pichler.de>
 * @copyright  2007 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/wiki/phpUnderControl
 */
abstract class phpucAbstractCommand
{
    /**
     * Factory method for the different cli modes.
     *
     * @param phpucConsoleArgs $args The console arguments.
     * 
     * @return phpucAbstractCommand
     */
    public static function createCommand( phpucConsoleArgs $args )
    {
        // Generate class name
        $className = sprintf( 'phpuc%sCommand', ucfirst( $args->command ) );
        
        if ( class_exists( $className, true ) === false )
        {
            printf( 'Unknown command "%s" used.%s', $args->command, PHP_EOL );
            exit( 1 );
        }
        
        return new $className( $args );
    }
    
    /**
     * The console argument object.
     *
     * @type phpucConsoleArgs
     * @var phpucConsoleArgs $args
     */
    protected $args = null;
    
    /**
     * List of command specific tasks.
     *
     * @type array<phpucTaskI>
     * @var array(phpucTaskI)
     */
    protected $tasks = null;
    
    /**
     * Protected ctor that takes the tasks and console arguments as parameters.
     * 
     * @param phpucConsoleArgs $args The console arguments.
     */
    protected final function __construct( phpucConsoleArgs $args )
    {
        $this->args = $args;
    }
    
    /**
     * Validates all command tasks.
     *
     * @return void
     */
    public function validate()
    {
        foreach ( $this->createTasks() as $task )
        {
            $task->validates();
        }
    }
    
    /**
     * Executes all command tasks.
     * 
     * @return void
     */
    public function execute()
    {
        foreach ( $this->createTasks() as $task )
        {
            $task->execute();
        }
    }
    
    /**
     * Creates a set of command specific tasks.
     *
     * @return array(phpucTaskI)
     */
    public final function createTasks()
    {
        if ( $this->tasks === null )
        {
            $this->tasks = $this->doCreateTasks();
        }
        return $this->tasks;
    }
    
    /**
     * Creates all command specific {@link phpucTaskI} objects.
     * 
     * @return array(phpucTaskI)
     */
    protected abstract function doCreateTasks();
}
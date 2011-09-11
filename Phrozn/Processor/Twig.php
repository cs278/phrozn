<?php
/**
 * Copyright 2011 Victor Farazdagi
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at 
 *
 *          http://www.apache.org/licenses/LICENSE-2.0 
 *
 * Unless required by applicable law or agreed to in writing, software 
 * distributed under the License is distributed on an "AS IS" BASIS, 
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 * See the License for the specific language governing permissions and 
 * limitations under the License. 
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Processor;
use Phrozn\Autoloader as Loader,
    Phrozn\Path\Project as ProjectPath;

/**
 * Twig templates processor
 *
 * @category    Phrozn
 * @package     Phrozn\Processor
 * @author      Victor Farazdagi
 */
class Twig
    extends Base
    implements \Phrozn\Processor 
{
    /**
     * Reference to twig engine environment object
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * If configuration options are passes then twig environment 
     * is initialized right away
     *
     * @param array $options Processor options
     *
     * @return \Phrozn\Processor\Twig
     */
    public function __construct($options = array())
    {
        $path = Loader::getInstance()->getPath('library');

        // Twig uses perverted file naming (due to absense of NSs at a time it was written)
        // so fire up its own autoloader
        require_once $path . '/Vendor/Twig/Autoloader.php';
        \Twig_Autoloader::register();

        if (count($options)) {
            $this->setConfig($options)
                 ->getEnvironment();
        }
    }

    /**
     * Parse the incoming template
     *
     * @param string $tpl Source template content
     * @param array $vars List of variables passed to template engine
     *
     * @return string Processed template
     */
    public function render($tpl, $vars = array())
    {
        $config = $this->getConfig();
        $environment = $this->getEnvironment();
        $loader = $environment->getLoader();

        // Last thing to do before the preceedings begin is install the Twig string
        // loader.
        if (!$loader instanceof \Twig_Loader_Chain) {
            // Replace the current loader with the chain loader inserting
            // the replaced loader into the chain loader. Phew!
            $environment->setLoader(new \Twig_Loader_Chain(array($loader)));
            $loader = $environment->getLoader();
        }
        $loader->addLoader(new \Twig_Loader_String);

        $template = $environment->loadTemplate($tpl);

        $content = $template->render($vars);

        return $content;
    }

    /**
     * Fetches a configured Twig renderer.
     *
     * @return \Twig_Environment
     */
    public function getEnvironment()
    {
        if (null === $this->twig) {
            $this->twig = $this->instantiateEnvironment();
        }

        return $this->twig;
    }

    /**
     * Sets the Twig environment.
     *
     * @param \Twig_Environment $twig
     *
     * @return Twig
     */
    public function setEnvironment(\Twig_Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * Constructs a default Twig renderer.
     *
     * @return \Twig_Environment
     */
    protected function instantiateEnvironment()
    {
        return new \Twig_Environment(new \Twig_Loader_Chain, $this->getConfig());
    }
}

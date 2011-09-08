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
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 * @copyright   2011 Victor Farazdagi
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Phrozn\Site\View;
use Phrozn\Site,
    Phrozn\Site\View\OutputPath\Entry as OutputFile,
    Phrozn\Processor\Twig as Processor;

/**
 * Twig View
 *
 * @category    Phrozn
 * @package     Phrozn\Site\View
 * @author      Victor Farazdagi
 */
class Twig 
    extends Base
    implements Site\View
{
    /**
     * Set input file path
     *
     * @param string $file Path to file
     *
     * @return \Phrozn\Site\View
     */
    public function setInputFile($path)
    {
        if ($path !== null) {
            $processors = $this->getProcessors();

            if (!isset($processors['\\Phrozn\\Processor\\Twig'])) {
                $project = new \Phrozn\Path\Project($path);

                $this->addProcessor(new Processor(array(
                    'paths' => array($project->get()))));
            }
        }

        return parent::setInputFile($path);
    }

    /**
     * Render view. Twig views are rendered within layout.
     *
     * @param array $vars List of variables passed to text processors
     *
     * @return string
     */
    public function render($vars = array())
    {
        $view = parent::render($vars);
        if ($this->hasLayout()) {
            // inject global site and front matter options into template
            $vars = array_merge($vars, $this->getParams());
            $view = $this->applyLayout($view, $vars);
        }
        return $view;
    }

    /**
     * Get output file path
     *
     * @return string
     */
    public function getOutputFile()
    {
        $path = new OutputFile($this);
        return $path->get();
    }
}

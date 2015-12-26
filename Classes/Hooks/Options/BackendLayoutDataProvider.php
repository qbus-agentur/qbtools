<?php
namespace Qbus\Qbtools\Hooks\Options;

use TYPO3\CMS\Backend\View\BackendLayout\BackendLayout;
use TYPO3\CMS\Backend\View\BackendLayout\DataProviderContext;
use TYPO3\CMS\Backend\View\BackendLayout\BackendLayoutCollection;

class BackendLayoutDataProvider implements \TYPO3\CMS\Backend\View\BackendLayout\DataProviderInterface
{
    /**
     * @param  DataProviderContext     $dataProviderContext
     * @param  BackendLayoutCollection $backendLayoutCollection
     * @return void
     */
    public function addBackendLayouts(DataProviderContext $dataProviderContext, BackendLayoutCollection $backendLayoutCollection)
    {
        $BEfunc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Utility\BackendUtility');
        $pageTSconfig = $BEfunc->getPagesTSconfig(1);

        if (isset($pageTSconfig['tx_qbtools.']['backend_layout.'])) {
            foreach ($pageTSconfig['tx_qbtools.']['backend_layout.'] as $id => $layout) {
                if (substr($id, -1) != '.') {
                    continue;
                }

                $id = substr($id, 0, -1);

                $backendLayout = $this->createBackendLayout($id, $layout);
                $backendLayoutCollection->add($backendLayout);
            }
        }
    }

    /**
     * Gets a backend layout by (regular) identifier.
     *
     * @param  string             $identifier
     * @param  integer            $pageId
     * @return NULL|BackendLayout
     */
    public function getBackendLayout($identifier, $pageId)
    {
        $BEfunc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Backend\Utility\BackendUtility');
        $pageTSconfig = $BEfunc->getPagesTSconfig(1);


        if (isset($pageTSconfig['tx_qbtools.']['backend_layout.'][$identifier . '.'])) {
            return $this->createBackendLayout($identifier, $pageTSconfig['tx_qbtools.']['backend_layout.'][$identifier . '.']);
        }

        return null;
    }

    protected function typoscriptToString(array $config)
    {
        $str = '';

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $str .= sprintf("%s {\n%s\n}\n", substr($key, 0, -1), $this->typoscriptToString($value));
            } else {
                $str .= sprintf("%s = %s\n", $key, $value);
            }
        }

        return $str;
    }

    /**
     * Creates a new backend layout using the given record data.
     *
     * @param  string        $id
     * @param  array         $data
     * @return BackendLayout
     */
    protected function createBackendLayout($id, $layout)
    {
        $layout['uid'] = $id;

        if (isset($layout['config.'])) {
            $layout['config'] = array('backend_layout.' => $layout['config.']);
            unset($layout['config.']);

            $layout['config'] = $this->typoscriptToString($layout['config']);
        }

        if (!isset($layout['title'])) {
            $layout['title'] = 'Untitled';
        }

        $layout['icon'] = $this->getIconPath($layout['icon']);

        $backendLayout = BackendLayout::create($layout['uid'], $layout['title'], $layout['config']);
        $backendLayout->setIconPath($this->getIconPath($layout['icon']));
        $backendLayout->setData($layout);

        return $backendLayout;
    }

    /**
     * Gets and sanitizes the icon path.
     *
     * @param  string $icon Name of the icon file
     * @return string
     */
    protected function getIconPath($icon)
    {
        $iconPath = '';
        if (!empty($icon)) {
            $iconPath = $icon;
        }

        return $iconPath;
    }
}

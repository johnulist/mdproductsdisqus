<?php
/**
 * 2016 Michael Dekker
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@michaeldekker.com so we can send you a copy immediately.
 *
 *  @author    Michael Dekker <prestashop@michaeldekker.com>
 *  @copyright 2016 Michael Dekker
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class MDProductsDisqus extends Module
{
    const DISQUS_USERNAME = 'MDPRODUCTSDISQUS_USERNAME';

    /**
     * MDProductsDisqus constructor.
     */
    public function __construct()
    {
        $this->name = 'mdproductsdisqus';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'Michael Dekker';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Disqus on product page');
        $this->description = $this->l('Add Disqus comments to your product pages');
    }

    /**
     * Install the module
     *
     * @return bool Whether the module has been successfully installed
     * @throws PrestaShopException
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $this->registerHook('displayFooterProduct');
        } else {
            $this->registerHook('displayProductTab');
            $this->registerHook('displayProductTabContent');
        }

        return true;
    }

    /**
     * Uninstall the module
     *
     * @return bool Whether the module has been successfully uninstalled
     */
    public function uninstall()
    {
        Configuration::deleteByName(self::DISQUS_USERNAME);

        $this->unregisterHook('displayFooterProduct');
        $this->unregisterHook('displayProductTab');
        $this->unregisterHook('displayProductTabContent');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->postProcess();

        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderGeneralOptions();
    }

    /**
     * Render the General options form
     *
     * @return string HTML
     */
    protected function renderGeneralOptions()
    {
        $helper = new HelperOptions();
        $helper->id = 1;
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;

        return $helper->generateOptions(array_merge($this->getGeneralOptions()));
    }

    /**
     * Get available general options
     *
     * @return array General options
     */
    protected function getGeneralOptions()
    {
        return array(
            'locales' => array(
                'title' => $this->l('General Settings'),
                'icon' => 'icon-server',
                'fields' => array(
                    self::DISQUS_USERNAME => array(
                        'title' => $this->l('Disqus username'),
                        'type' => 'text',
                        'name' => self::DISQUS_USERNAME,
                        'value' => Configuration::get(self::DISQUS_USERNAME),
                        'validation' => 'isString',
                        'cast' => 'strval',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'button',
                ),
            ),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $output = '';
        if (Tools::isSubmit('submitOptionsconfiguration') || Tools::isSubmit('submitOptions')) {
            $output .= $this->postProcessGeneralOptions();
        }

        return $output;
    }

    /**
     * Process General Options
     */
    protected function postProcessGeneralOptions()
    {
        $username = Tools::getValue(self::DISQUS_USERNAME);

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (Shop::getContext() == Shop::CONTEXT_ALL) {
                $this->updateAllValue(self::DISQUS_USERNAME, $username);
            } elseif (is_array(Tools::getValue('multishopOverrideOption'))) {
                $idShopGroup = (int) Shop::getGroupFromShop($this->getShopId(), true);
                $multishopOverride = Tools::getValue('multishopOverrideOption');
                if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                    foreach (Shop::getShops(false, $this->getShopId()) as $idShop) {
                        if ($multishopOverride[self::DISQUS_USERNAME]) {
                            Configuration::updateValue(self::DISQUS_USERNAME, $username, false, $idShopGroup, $idShop);
                        }
                    }
                } else {
                    $idShop = (int) $this->getShopId();
                    if ($multishopOverride[self::DISQUS_USERNAME]) {
                        Configuration::updateValue(self::DISQUS_USERNAME, $username, false, $idShopGroup, $idShop);
                    }
                }
            }
        } else {
            Configuration::updateValue(self::DISQUS_USERNAME, $username);
        }
    }

    /**
     * Hook to product footer
     *
     * @return string Hook HTML
     * @throws Exception
     * @throws SmartyException
     */
    public function hookDisplayFooterProduct()
    {
        $this->context->smarty->assign(array(
            'id_product' => (int) Tools::getValue('id_product'),
            'disqus_username' => Configuration::get(self::DISQUS_USERNAME),
        ));

        return $this->display(__FILE__, 'disqusfooter.tpl');
    }

    /**
     * Hook to product tab
     *
     * @return string Hook HTML
     * @throws Exception
     * @throws SmartyException
     */
    public function hookDisplayProductTab()
    {
        $this->context->smarty->assign(array(
            'disqus_username' => Configuration::get(self::DISQUS_USERNAME),
        ));

        return $this->display(__FILE__, 'tab.tpl');
    }

    /**
     * Hook to product tab content
     *
     * @param array $params Hook parameters
     * @return string Hook HTML
     * @throws Exception
     * @throws SmartyException
     */
    public function hookDisplayProductTabContent($params)
    {
        /** @var Product $product */
        $product = $params['product'];
        $this->context->smarty->assign(array(
            'id_product' => $product->id,
            'disqus_username' => Configuration::get(self::DISQUS_USERNAME),
        ));

        return $this->display(__FILE__, 'tabcontent.tpl');
    }

    /**
     * Update configuration value in ALL contexts
     *
     * @param string $key    Configuration key
     * @param mixed  $values Configuration values, can be string or array with id_lang as key
     * @param bool   $html   Contains HTML
     */
    public function updateAllValue($key, $values, $html = false)
    {
        foreach (Shop::getShops() as $shop) {
            Configuration::updateValue($key, $values, $html, $shop['id_shop_group'], $shop['id_shop']);
        }
        Configuration::updateGlobalValue($key, $values, $html);
    }

    /**
     * Get the Shop ID of the current context
     * Retrieves the Shop ID from the cookie
     *
     * @return int Shop ID
     */
    public function getShopId()
    {
        $cookie = Context::getContext()->cookie->getFamily('shopContext');

        return (int) Tools::substr($cookie['shopContext'], 2, count($cookie['shopContext']));
    }
}

<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Dbaboutus extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        require_once(dirname(__FILE__).'/classes/DbAboutUsAuthor.php');
        require_once(dirname(__FILE__).'/classes/DbAboutUsSpeciality.php');
        require_once(dirname(__FILE__).'/classes/DbAboutUsTag.php');

        if(file_exists(dirname(__FILE__).'/premium/DbPremium.php')){
            require_once(dirname(__FILE__).'/premium/DbPremium.php');
            $this->premium = 1;
        } else {
            $this->premium = 0;
        }

        $this->name = 'dbaboutus';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'DevBlinders';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DB About Us');
        $this->description = $this->l('Quienes somos con paginas de autores personalizada');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('DBABOUTUS_URL', 'quienes-somos');
        Configuration::updateValue('DBABOUTUS_TITLE', 'Quiénes Somos');

        $this->createTabs();

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('moduleRoutes');
    }

    public function uninstall()
    {
        Configuration::deleteByName('DBABOUTUS_URL');
        Configuration::deleteByName('DBABOUTUS_TITLE');

        $this->deleteTabs();

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Create Tabs
     */
    public function createTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbAbout');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutAuthor');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutSpecialty');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutTag');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutSetting');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }

        // Tabs
        if (!Tab::getIdFromClassName('AdminDevBlinders')) {
            $parent_tab = new Tab();
            $parent_tab->name = array();
            foreach (Language::getLanguages(true) as $lang)
                $parent_tab->name[$lang['id_lang']] = $this->l('DevBlinders');

            $parent_tab->class_name = 'AdminDevBlinders';
            $parent_tab->id_parent = 0;
            $parent_tab->module = $this->name;
            $parent_tab->add();

            $id_full_parent = $parent_tab->id;
        } else {
            $id_full_parent = Tab::getIdFromClassName('AdminDevBlinders');
        }
        
        // Principal Sobre Nosotros
        $parent = new Tab();
        $parent->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $parent->name[$lang['id_lang']] = $this->l('Quiénes somos');

        $parent->class_name = 'AdminDbAbout';
        $parent->id_parent = $id_full_parent;
        $parent->module = $this->name;
        $parent->icon = 'account_circle';
        $parent->add();

        // Autor
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Autor');

        $tab_config->class_name = 'AdminDbAboutAuthor';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Especialidad
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Especialidad');

        $tab_config->class_name = 'AdminDbAboutSpecialty';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Tags
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Etiquetas de expertos');

        $tab_config->class_name = 'AdminDbAboutTag';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();

        // Configuración
        $tab_config = new Tab();
        $tab_config->name = array();
        foreach (Language::getLanguages(true) as $lang)
            $tab_config->name[$lang['id_lang']] = $this->l('Configuración');

        $tab_config->class_name = 'AdminDbAboutSetting';
        $tab_config->id_parent = $parent->id;
        $tab_config->module = $this->name;
        $tab_config->add();
    }

    /**
     * Delete Tabs
     */
    public function deleteTabs()
    {
        // Tabs
        $idTabs = array();
        $idTabs[] = Tab::getIdFromClassName('AdminDbAbout');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutAuthor');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutSpecialty');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutTag');
        $idTabs[] = Tab::getIdFromClassName('AdminDbAboutSetting');

        foreach ($idTabs as $idTab) {
            if ($idTab) {
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitDbaboutusModule')) == true) {
            $this->postProcess();
        }

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDbaboutusModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        if($this->premium == 1){
            return DbPremium::getConfigFormGeneral();
        }

        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configuración'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(                   
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'name' => 'DBABOUTUS_TITLE',
                        'label' => $this->l('Título'),
                        'lang'  => true,
                    ),
                    array(
                        'col' => 9,
                        'type' => 'text',
                        'name' => 'DBABOUTUS_METATITLE',
                        'label' => $this->l('Meta-título'),
                        'lang'  => true,
                    ),
                    array(
                        'col' => 9,
                        'type' => 'text',
                        'name' => 'DBABOUTUS_METADESC',
                        'label' => $this->l('Meta descripción'),
                        'lang'  => true,
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Path general, ejem: sobre-nosotros'),
                        'name' => 'DBABOUTUS_URL',
                        'label' => $this->l('Url'),
                        'lang'  => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'DBABOUTUS_SHORT_DESC',
                        'label' => $this->l('Descripción corta'),
                        'autoload_rte' => true,
                        'rows' => 5,
                        'cols' => 40,
                        'lang'  => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $languages = Language::getLanguages(false);
        $values = array();

        foreach ($languages as $lang){         
            $values['DBABOUTUS_URL'][$lang['id_lang']] = Configuration::get('DBABOUTUS_URL', $lang['id_lang']);
            $values['DBABOUTUS_TITLE'][$lang['id_lang']] = Configuration::get('DBABOUTUS_TITLE', $lang['id_lang']);
            $values['DBABOUTUS_SHORT_DESC'][$lang['id_lang']] = Configuration::get('DBABOUTUS_SHORT_DESC', $lang['id_lang']);
            $values['DBABOUTUS_LARGE_DESC'][$lang['id_lang']] = Configuration::get('DBABOUTUS_LARGE_DESC', $lang['id_lang']);
            $values['DBABOUTUS_METATITLE'][$lang['id_lang']] = Configuration::get('DBABOUTUS_METATITLE', $lang['id_lang']);
            $values['DBABOUTUS_METADESC'][$lang['id_lang']] = Configuration::get('DBABOUTUS_METADESC', $lang['id_lang']);
        }
        return $values;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $languages = Language::getLanguages(false);
        $id_shop_group = (int)Context::getContext()->shop->id_shop_group;
        $id_shop = (int)Context::getContext()->shop->id;

        foreach ($form_values as $name => $key) {
            if(is_array($key)){
                $values = array();
                foreach ($languages as $lang){
                    $values[$lang['id_lang']] = Tools::getValue($name . '_'.(int)$lang['id_lang']);
                }
                Configuration::updateValue($name, $values, true, $id_shop_group, $id_shop);
            } else {
                Configuration::updateValue($name, Tools::getValue($name), true, $id_shop_group, $id_shop);
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/dbaboutus.css');
    }

    /**
     * Add Routes
     */
    public function hookModuleRoutes($params)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $url = Configuration::get('DBABOUTUS_URL', $id_lang);


        $my_routes = array(
            // Home About Us
            'module-dbaboutus-home' => array(
                'controller' => 'home',
                'rule' => $url.'/',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'dbaboutus',
                ),
            ),

            // Author
            'module-dbaboutus-author' => array(
                'controller' => 'author',
                'rule' =>       $url.'/{rewrite}.html',
                'keywords' => array(
                    'rewrite' =>    array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'dbaboutus',
                ),
            ), 
        );

        return $my_routes;
    }

}

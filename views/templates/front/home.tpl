{*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file='page.tpl'}

{block name='hook_extra'}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {foreach from=$breadcrumb.links item=path name=breadcrumb}
            {
                "@type": "ListItem",
                "position": {$smarty.foreach.breadcrumb.iteration|escape:'htmlall':'UTF-8'},
                "name": "{$path.title|escape:'htmlall':'UTF-8'}",
                "item": "{$path.url|escape:'htmlall':'UTF-8'}"
            }{if not $smarty.foreach.breadcrumb.last},{/if}
            {/foreach}
        ]
    }
    </script>
{/block}

{include file='module:dbaboutus/views/templates/front/_partials/breadcrumb.tpl'}

{block name="content_wrapper"}
    <div id="content-wrapper" class="center-column authors row">
        <h1>{$title|escape:'htmlall':'UTF-8'}</h1>
        <div class="short_description">
            {$short_desc nofilter}
        </div>
        {if $authors|count > 0}
        <div class="group_authors">
            <p class="title">Equipo</p>
            <ul class="team_authors">
                {foreach from=$authors item=author}
                    <li>
                        <a href="{DbAboutUsAuthor::getLink($author.link_rewrite|escape:'htmlall':'UTF-8')}" class="link_author">
                            <img src="{$path_img|escape:'htmlall':'UTF-8'}{$author.id_dbaboutus_author|escape:'htmlall':'UTF-8'}.jpg" alt="{$author.name|escape:'htmlall':'UTF-8'}">
                            <span class="name">{$author.name|escape:'htmlall':'UTF-8'}</span>
                            <span class="work">{$author.profession|escape:'htmlall':'UTF-8'}</span>
                            <span class="link">{l s='Ver más' mod='dbaboutus'} <span class="material-icons">keyboard_arrow_right</span></span>
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        {/if}
        <div class="large_description">
            {$large_desc nofilter}
        </div>

    </div>
{/block}
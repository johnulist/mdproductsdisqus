{*
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
*}
{if isset($disqus_username) && !empty($disqus_username)}
	<div id="disqusTab">
		<div id="disqus_thread"></div>
		<script>
			var disqus_config = function () {
				this.language = '{Context::getContext()->language->iso_code|escape:'javascript':'UTF-8'}';
				this.page.url = '{Tools::getHttpHost(true)|cat:$smarty.server.REQUEST_URI|escape:'javascript':'UTF-8'}';
				this.page.identifier = '{'product-'|cat:Context::getContext()->language->iso_code|strtolower|cat:'-'|cat:$id_product|escape:'javascript':'UTF-8'}';
			};

			(function () {
				var d = document, s = d.createElement('script');

				s.src = '//{$disqus_username|escape:'javascript':'UTF-8'}.disqus.com/embed.js';

				s.setAttribute('data-timestamp', +new Date());
				(d.head || d.body).appendChild(s);
			})();
		</script>
		<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
	</div>
{/if}
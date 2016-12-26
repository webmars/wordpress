<?php

// 特色图片支持
	add_theme_support('post-thumbnails');
// 替换avatar头像源
function replace_avatar($avatar) {
	$avatar = str_replace(array("www.gravatar.com", "0.gravatar.com", "1.gravatar.com", "2.gravatar.com"), "cn.gravatar.com", $avatar);
	return $avatar;
}
add_filter('get_avatar', 'replace_avatar', 10, 3);
// 图片链接
function auto_post_link($content) {
	global $post;
        $content = preg_replace('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', "<a href=\"$2\" title=\"".$post->post_title."\"   target=\"_blank\"><img src=\"$2\" alt=\"".$post->post_title."\"/></a>", $content);
	return $content;
}
add_filter ('the_content', 'auto_post_link',0);
// 链接弹出新窗口
function autoblank($text) {
$return = str_replace('<a', '<a target="_blank"', $text);
return $return;
}
add_filter('the_content', 'autoblank');
// 禁用 WordPress 4.4+ 的响应式图片功能
add_filter( 'max_srcset_image_width', create_function( '', 'return 1;' ) );
// 小工具
if (function_exists('register_sidebar')) {
	register_sidebar(array('name' => '首页侧边栏', 'id' => 'sidebar-1', 'description' => '显示在首页及分类归档页', 'before_widget' => '<aside id="%1$s" class="widget %2$s">', 'after_widget' => '</aside>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>',));
	register_sidebar(array('name' => '正文侧边栏', 'id' => 'sidebar-2', 'description' => '显示在正文、页面', 'before_widget' => '<aside id="%1$s" class="widget %2$s">', 'after_widget' => '</aside>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>',));
	register_sidebar(array('name' => '分类归档侧边栏', 'id' => 'sidebar-5', 'description' => '显示在归档页、搜索、404页 ', 'before_widget' => '<aside id="%1$s" class="widget %2$s">', 'after_widget' => '</aside>', 'before_title' => '<h3 class="widget-title">', 'after_title' => '</h3>',));
	register_sidebar(array('name' => '正文底部小工具', 'id' => 'sidebar-3', 'description' => '显示在正文底部', 'before_widget' => '<div id="single-widget"><aside id="%1$s" class="widget %2$s">', 'after_widget' => '</aside></div>', 'before_title' => '<h3 class="widget-title"><span class="s-icon"></span>', 'after_title' => '</h3>',));
}
// 自定义菜单
register_nav_menus(array('top-menu' => __('顶部菜单'), 'header-menu' => __('导航菜单'), 'mini-menu' => __('移动版菜单')));
// feed
add_theme_support('automatic-feed-links');
// 移除头部冗余代码
remove_action( 'wp_head', 'wp_generator' );// WP版本信息
remove_action( 'wp_head', 'rsd_link' );// 离线编辑器接口
remove_action( 'wp_head', 'wlwmanifest_link' );// 同上
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );// 上下文章的url
remove_action( 'wp_head', 'feed_links', 2 );// 文章和评论feed
remove_action( 'wp_head', 'feed_links_extra', 3 );// 去除评论feed
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );// 短链接
remove_action('wp_head', 'wp_resource_hints', 2);
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
// 移除 WordPress 加载的JS和CSS链接中的版本号
function remove_wpver($src) {
	if (strpos($src, 'ver=')) $src = remove_query_arg('ver', $src);
	return $src;
}
add_filter('style_loader_src', 'remove_wpver', 999);
add_filter('script_loader_src', 'remove_wpver', 999);
// 自动缩略图
function catch_image() {
	global $post, $posts;
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches[1][0];
	if (empty($first_img)) {
		echo get_bloginfo('stylesheet_directory');
		echo '/img/default.jpg';
	}
	return $first_img;
}
// 所有图片
function all_img($soContent) {
	$soImages = '~<img [^\>]*\ />~';
	preg_match_all($soImages, $soContent, $thePics);
	$allPics = count($thePics);
	if ($allPics > 0) {
		$count = 0;
		foreach ($thePics[0] as $v) {
			if ($count == 4) {
				break;
			} else {
				echo $v;
			}
			$count++;
		}
	}
}
//禁止垃圾评论提交到数据库
function comments_fuckspam($comment) {
	if (is_user_logged_in()) {
		return $comment;
	} //登录用户无压力...
	if (wp_blacklist_check($comment['comment_author'], $comment['comment_author_email'], $comment['comment_author_url'], $comment['comment_content'], isset($comment['comment_author_IP']), isset($comment['comment_agent']))) {
		header("Content-type: text/html; charset=utf-8");
		wp_die('
抱谦，本次提交失败！<br>可能的原因：请检查您的输入是否有禁止词汇！！<br><a href="javascript:history.go(-1);">返回前一页</a>
');
	} else {
		return $comment;
	}
}
add_filter('preprocess_comment', 'comments_fuckspam');
// 禁止全英文和日文评论
function comments_chinese_please($incoming_comment) {
	$chinese = '/[一-龥]/u';
	$nonchinese = '/[ぁ-ん]+|[ァ-ヴ]+|[А-я]+|[갂-줎]+|[줐-쥯]+|[쥱-짛]+|[짞-쪧]+|[쪨-쬊]+|[쬋-쭬]+|[쵡-힝]+|[؟-ض]+|[ط-ل]+|[م-م]+|[؟-ض]+|[ط-ل]+|[م-م]+|[ก-๛]+/u';
	if (!preg_match($chinese, $incoming_comment['comment_content'])) {
		wp_die('抱谦，本次提交失败。Sorry,comment failed to post.<br>可能原因：纯英文被禁止！！ Try some Chinese please！<br><a href="javascript:history.go(-1);">返回前一页</a>');
	}
	if (preg_match($nonchinese, $incoming_comment['comment_content'])) {
		wp_die('抱谦，本次提交失败。Sorry,comment failed to post.<br>可能原因：评论不支持中文/英文以外语种！ Try some Chinese please！<br><a href="javascript:history.go(-1);">返回前一页</a>');
	}
	return ($incoming_comment);
}
add_filter('preprocess_comment', 'comments_chinese_please');
//评论字数限制
function comments_length_limit($commentdata) {
	$minCommentlength = 5;
	$maxCommentlength = 120;
	$pointCommentlength = mb_strlen($commentdata['comment_content'], 'UTF8');
	if ($pointCommentlength < $minCommentlength) {
		header("Content-type: text/html; charset=utf-8");
		wp_die('
抱歉，本次提交失败！<br>可能原因，内容太少，请至少输入' . $minCommentlength . '个字（已输入' . $pointCommentlength . '个字）<br><a href="javascript:history.go(-1);">返回前一页</a>
');
		exit;
	}
	if ($pointCommentlength > $maxCommentlength) {
		header("Content-type: text/html; charset=utf-8");
		wp_die('
抱歉，本次提交失败！<br>可能原因：内容过多，请少于' . $maxCommentlength . '个字（已输入' . $pointCommentlength . '个字）<br><a href="javascript:history.go(-1);">返回前一页</a>
');
		exit;
	}
	return $commentdata;
}
add_filter('preprocess_comment', 'comments_length_limit');
// 评论链接新窗口
function commentauthor($comment_ID = 0) {
	$url = get_comment_author_url($comment_ID);
	$author = get_comment_author($comment_ID);
	if (empty($url) || 'http://' == $url) echo $author;
	else echo "<a href='$url' rel='external nofollow' target='_blank' class='url'>$author</a>";
}
// 评论添加@用户
function comment_add_at( $comment_text, $comment = '') {
  if( $comment->comment_parent > 0) {
    $comment_text = '<a href="#comment-' . $comment->comment_parent . '">@'.get_comment_author( $comment->comment_parent ) . '</a> ' . $comment_text;
  }

  return $comment_text;
}
add_filter( 'comment_text' , 'comment_add_at', 20, 2);
// 移除 REST API 端点
remove_action('rest_api_init', 'wp_oembed_register_route');
// 禁用 oEmbed 自动发现功能
add_filter('embed_oembed_discover', '__return_false');
// 不要过滤 oEmbed 结果
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
// 主题小工具
require get_template_directory() . '/inc/functions/widgets.php';
// 主题设置
require get_template_directory() . '/inc/theme-options.php';
// 评论模板
require get_template_directory() . '/inc/functions/comment-template.php';
// 评论通知
require get_template_directory() . '/inc/functions/notify.php';
// 热门文章
require get_template_directory() . '/inc/functions/hot-post.php';
// 分页
require get_template_directory() . '/inc/functions/pagenavi.php';
// 面包屑导航
require get_template_directory() . '/inc/functions/breadcrumb.php';
// 文字展开
require get_template_directory() . '/inc/functions/section.php';
// 禁止代码标点转换
remove_filter('the_content', 'wptexturize');
// 链接管理
add_filter('pre_option_link_manager_enabled', '__return_true');
// 默认菜单
function default_menu() {
	require get_template_directory() . '/inc/default-menu.php';
}
// 分页
function total_page_nav() {
	global $wp_query;
	if ($wp_query->max_num_pages > 1): ?>
		<nav id="nav-below">
			<div class="nav-next"><?php previous_posts_link(上一页); ?></div>
			<div class="nav-previous"><?php next_posts_link(下一页); ?></div>
		</nav>
	<?php
	endif;
}
// 去掉描述P标签
function deletehtml($description) {
	$description = trim($description);
	$description = strip_tags($description, "");
	return ($description);
}
add_filter('category_description', 'deletehtml');
// 禁止后台加载谷歌字体
function wp_remove_open_sans_from_wp_core() {
	wp_deregister_style('open-sans');
	wp_register_style('open-sans', false);
	wp_enqueue_style('open-sans', '');
}
add_action('init', 'wp_remove_open_sans_from_wp_core');
/**  移除菜单的多余CSS选择器*/
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
function my_css_attributes_filter($var) {
	return is_array($var) ? array_intersect($var, array('current-menu-item', 'current-post-ancestor', 'current-menu-ancestor', 'current-menu-parent')) : '';
}
// 字数统计
function count_words($text) {
	global $post;
	if ('' == $text) {
		$text = $post->post_content;
		if (mb_strlen($output, 'UTF-8') < mb_strlen($text, 'UTF-8')) $output.= '共 ' . mb_strlen(preg_replace('/\s/', '', html_entity_decode(strip_tags($post->post_content))), 'UTF-8') . '字';
		return $output;
	}
}
// 编辑器增强
function enable_more_buttons($buttons) {
	$buttons[] = 'hr';
	$buttons[] = 'del';
	$buttons[] = 'sub';
	$buttons[] = 'sup';
	$buttons[] = 'fontselect';
	$buttons[] = 'fontsizeselect';
	$buttons[] = 'cleanup';
	$buttons[] = 'styleselect';
	$buttons[] = 'wp_page';
	$buttons[] = 'anchor';
	$buttons[] = 'backcolor';
	return $buttons;
}
add_filter("mce_buttons_3", "enable_more_buttons");
// 添加按钮
add_action('after_wp_tiny_mce', 'bolo_after_wp_tiny_mce');
function bolo_after_wp_tiny_mce($mce_settings) {
?>
<script type="text/javascript">
QTags.addButton( 'file', '站外链接', '<div id="down"><a href="http://www.idcfan.com">直达金山云官方网站</a></div>' );
QTags.addButton( 'idc', 'IDC', '<h3>厂商信息</h3><h3>厂商简介</h3><h3>主营业务</h3>' );
QTags.addButton( 'videos', '添加视频', "[videos]视频分享代码[/videos]" );
function bolo_QTnextpage_arg1() {
}
</script>
<?php
}
// 视频
function screening($atts, $content = null) {
	return '<div class="screening"><a class="video" href="' . $content . '">播放视频</a></div>';
}
add_shortcode('videos', 'screening');
// 禁用工具栏
show_admin_bar(false);
// taxonomy标题
function setTitle() {
	$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
	echo $title = $term->name;
}
// 后台字体
function admin_lettering() {
	echo '<style type="text/css">
     body{ font-family: Microsoft YaHei;}
    </style>';
}
add_action('admin_head', 'admin_lettering');
//禁用后台文章可视化编辑器中的wpemoji插件
function disable_emoji9s_tinymce($plugins) {
	if (is_array($plugins)) {
		return array_diff($plugins, array('wpemoji'));
	} else {
		return array();
	}
}
//返回当前主题下img\smilies\下表情图片路径
function custom_smilie9s_src($old, $img) {
	return get_stylesheet_directory_uri() . '/img/sm/' . $img;
}
function init_smilie9s() {
	global $wpsmiliestrans;
	//默认表情文本与表情图片的对应关系(可自定义修改)
	$wpsmiliestrans = array(':mrgreen:' => 'icon_mrgreen.gif', ':neutral:' => 'icon_neutral.gif', ':twisted:' => 'icon_twisted.gif', ':arrow:' => 'icon_arrow.gif', ':shock:' => 'icon_eek.gif', ':smile:' => 'icon_smile.gif', ':???:' => 'icon_confused.gif', ':cool:' => 'icon_cool.gif', ':evil:' => 'icon_evil.gif', ':grin:' => 'icon_biggrin.gif', ':idea:' => 'icon_idea.gif', ':oops:' => 'icon_redface.gif', ':razz:' => 'icon_razz.gif', ':roll:' => 'icon_rolleyes.gif', ':wink:' => 'icon_wink.gif', ':cry:' => 'icon_cry.gif', ':eek:' => 'icon_surprised.gif', ':lol:' => 'icon_lol.gif', ':mad:' => 'icon_mad.gif', ':sad:' => 'icon_sad.gif', '8-)' => 'icon_cool.gif', '8-O' => 'icon_eek.gif', ':-(' => 'icon_sad.gif', ':-)' => 'icon_smile.gif', ':-?' => 'icon_confused.gif', ':-D' => 'icon_biggrin.gif', ':-P' => 'icon_razz.gif', ':-o' => 'icon_surprised.gif', ':-x' => 'icon_mad.gif', ':-|' => 'icon_neutral.gif', ';-)' => 'icon_wink.gif', '8O' => 'icon_eek.gif', ':(' => 'icon_sad.gif', ':)' => 'icon_smile.gif', ':?' => 'icon_confused.gif', ':D' => 'icon_biggrin.gif', ':P' => 'icon_razz.gif', ':o' => 'icon_surprised.gif', ':x' => 'icon_mad.gif', ':|' => 'icon_neutral.gif', ';)' => 'icon_wink.gif', ':!:' => 'icon_exclaim.gif', ':?:' => 'icon_question.gif',);
	//移除WordPress4.2版本更新所带来的Emoji前后台钩子同时挂上主题自带的表情路径
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');
	remove_action('admin_print_styles', 'print_emoji_styles');
	remove_filter('the_content_feed', 'wp_staticize_emoji');
	remove_filter('comment_text_rss', 'wp_staticize_emoji');
	remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
	add_filter('tiny_mce_plugins', 'disable_emoji9s_tinymce');
	add_filter('smilies_src', 'custom_smilie9s_src', 10, 2);
}
add_action('init', 'init_smilie9s', 5);
//后台登录限制
add_action('login_enqueue_scripts', 'login_protection');
function login_protection() {
	if (isset($_GET['user']) != 'lucifer') header('Location: http://www.idcfan.com/?errlogin');
}
//替换登录错误提示
function no_errors_please() {
	return 'ERROR!!';
}
add_filter('login_errors', 'no_errors_please');
//彻底禁止WordPress缩略图
add_filter('add_image_size', create_function('', 'return 1;'));
//禁止后台更新检查
add_filter('automatic_updater_disabled', '__return_true'); // 彻底关闭自动更新
remove_action('init', 'wp_schedule_update_checks'); // 关闭更新检查定时作业
wp_clear_scheduled_hook('wp_version_check'); // 移除已有的版本检查定时作业
wp_clear_scheduled_hook('wp_update_plugins'); // 移除已有的插件更新定时作业
wp_clear_scheduled_hook('wp_update_themes'); // 移除已有的主题更新定时作业
wp_clear_scheduled_hook('wp_maybe_auto_update'); // 移除已有的自动更新定时作业
remove_action('admin_init', '_maybe_update_core'); // 移除后台内核更新检查
remove_action('load-plugins.php', 'wp_update_plugins'); // 移除后台插件更新检查
remove_action('load-update.php', 'wp_update_plugins');
remove_action('load-update-core.php', 'wp_update_plugins');
remove_action('admin_init', '_maybe_update_plugins');
remove_action('load-themes.php', 'wp_update_themes'); // 移除后台主题更新检查
remove_action('load-update.php', 'wp_update_themes');
remove_action('load-update-core.php', 'wp_update_themes');
remove_action('admin_init', '_maybe_update_themes');
//禁止修订版本and自动保存
remove_action('pre_post_update', 'wp_save_post_revision');
add_filter('wp_revisions_to_keep', 'specs_wp_revisions_to_keep', 10, 2);
function specs_wp_revisions_to_keep($num, $post) {
	return 0;
}
add_action('wp_print_scripts', 'disable_autosave');
function disable_autosave() {
	wp_deregister_script('autosave');
}
//禁用wp-corn定时功能
defined('DISABLE_WP_CRON');
remove_action('init', 'wp_cron');
//禁用 XML-RPC 接口
add_filter('xmlrpc_enabled', '__return_false');
//彻底关闭 pingback
add_filter('xmlrpc_methods', 'wpjam_xmlrpc_methods');
function wpjam_xmlrpc_methods($methods) {
	$methods['pingback.ping'] = '__return_false';
	$methods['pingback.extensions.getPingbacks'] = '__return_false';
	return $methods;
}
remove_action('do_pings', 'do_all_pings', 10, 1); //禁用 pingbacks, enclosures, trackbacks
remove_action('publish_post', '_publish_post_hook', 5, 1); //去掉 _encloseme 和 do_ping 操作
//禁止加载语言包
add_filter('locale', 'disable_load_locale');
function disable_load_locale($locale) {
	$locale = (is_admin()) ? $locale : 'en_US';
	return $locale;
}
//IDC名单-分类列表页自定义
    function custom_posts_per_page($query){  
    if(is_category(array(85,86,87,88,89,90))){  
    $query->set('posts_per_page',20);
    $query->set('posts_orderby','title');
	$query->set('order','ASC');
        }  
    }  
    add_action('pre_get_posts','custom_posts_per_page'); 
//启用后台上传目录设置
if (get_option('upload_path') == 'wp-content/uploads' || get_option('upload_path') == null) {
	update_option('upload_path', 'thea/atts');
}
//后台顶部快捷条添加“主题选顶”
function custom_adminbar_menu( $meta = TRUE ) {  
    global $wp_admin_bar;  
        if ( !is_user_logged_in() ) { return; }  
        if ( !is_super_admin() || !is_admin_bar_showing() ) { return; }  
    $wp_admin_bar->add_menu( array(  
        'id' => 'custom_menu',  
        'title' => __( 'Theme Option' ),  
        'href' => 'themes.php?page=theme-options.php')  
    );  
}  
add_action( 'admin_bar_menu', 'custom_adminbar_menu', 100 );  
//压缩html代码
function wp_compress_html() {
	function wp_compress_html_main($buffer) {
		$initial = strlen($buffer);
		$buffer = explode("<!--wp-compress-html-->", $buffer);
		$count = count($buffer);
		for ($i = 0;$i <= $count;$i++) {
			if (stristr($buffer[$i], '<!--wp-compress-html no compression-->')) {
				$buffer[$i] = (str_replace("<!--wp-compress-html no compression-->", " ", $buffer[$i]));
			} else {
				$buffer[$i] = (str_replace("\t", " ", $buffer[$i]));
				$buffer[$i] = (str_replace("\n\n", "\n", $buffer[$i]));
				$buffer[$i] = (str_replace("\n", "", $buffer[$i]));
				$buffer[$i] = (str_replace("\r", "", $buffer[$i]));
				while (stristr($buffer[$i], '  ')) {
					$buffer[$i] = (str_replace("  ", " ", $buffer[$i]));
				}
			}
			$buffer_out.= $buffer[$i];
		}
		$final = strlen($buffer_out);
		$savings = ($initial - $final) / $initial * 100;
		$savings = round($savings, 2);
		$buffer_out.= "\n<!--before: $initial bytes; after: $final bytes; saved：$savings% -->";
		return $buffer_out;
	}
	ob_start("wp_compress_html_main");
}
add_action('get_header', 'wp_compress_html');
//文章外链跳转伪静态版
add_filter('the_content', 'link_jump', 999);
function link_jump($content) {
	preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/', $content, $matches);
	if ($matches) {
		foreach ($matches[2] as $val) {
			if (strpos($val, '://') !== false && strpos($val, home_url()) === false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i', $val) && !preg_match('/(ed2k|thunder|Flashget|flashget|qqdl):\/\//i', $val)) {
				$content = str_replace("href=\"$val\"", "href=\"" . home_url() . "/go/" . base64_encode($val) . "\" rel=\"nofollow\" target=\"_blank\"", $content);
			}
		}
	}
	return $content;
}

?>

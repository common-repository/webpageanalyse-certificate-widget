<?php
/*
  Plugin Name: WebPageAnalyse Certificate Widget
  Plugin URI: http://www.webpageanalyse.com
  Description: Shows a WebPageAnalyse certificate for your blog's domain
  Version: 1.0.1
  Author: webpageanalyse
  Author URI: http://www.webpageanalyse.com
  License: GNU LESSER GENERAL PUBLIC LICENSE (http://www.gnu.org/copyleft/lesser.html)
 */
class webpageanalyse_certificate_widget {
    private $pluginId = 'webpageanalyse_certificate_widget';
    private $i18n;

    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_uninstall_hook(__FILE__, array($this, 'uninstall'));

        $this->i18n = new webpageanalyse_certificate_widget_i18n(substr(get_bloginfo('language'), 0, 2));
    }

    public function init() {
        wp_register_sidebar_widget($this->pluginId, $this->i18n->_('name'), array($this, 'sidebar'));
        wp_register_widget_control($this->pluginId, $this->i18n->_('name'), array($this, 'settings'));
    }

    public function sidebar() {
        $targetDomain = $this->getTargetDomain();
        printf('<aside id="' . $this->pluginId . '" class="widget">' .
               '<a title="%s" href="http://%s" rel="nofollow">' .
               '<img src="http://%s/widget/%s/%s?s=wp" alt="%s">' .
               '</a></aside>',
            $this->i18n->_("link_title", $targetDomain),
            $this->i18n->_('domain'),
            $this->i18n->_('domain'),
            $this->getWidgetPath(),
            $targetDomain,
            $this->i18n->_("link_title", $targetDomain)
        );
    }

    public function settings() {
        if (count($_POST) > 0) {
            if (isset($_POST[$this->pluginId . '_domain'])) {
                update_option($this->pluginId . '_domain', htmlspecialchars($_POST[$this->pluginId . '_domain']));
            }
            if (isset($_POST[$this->pluginId . '_style'])) {
                update_option($this->pluginId . '_style', htmlspecialchars($_POST[$this->pluginId . '_style']));
            } else {
                update_option($this->pluginId . '_style', "wl");
            }
        }
        ?>
        <p>
            <label for="webpageanalyse_certificate_widget_domain"><?php echo $this->i18n->_("form.your_domain"); ?></label><br>
            <input type="text" id="webpageanalyse_certificate_widget_domain" name="webpageanalyse_certificate_widget_domain" value="<?php echo $this->getTargetDomain(); ?>">
        </p>
        <p>
            <label for="webpageanalyse_certificate_widget_style"><?php echo $this->i18n->_("form.style"); ?></label><br>
        <?php
        $styles = array("wl", "ws", "l", "s");
        foreach ($styles as $style) {
        ?>
            <input type="radio" id="webpageanalyse_certificate_widget_style" name="webpageanalyse_certificate_widget_style" value="<?php echo $style; ?>" <?php if ($this->getStyle() == $style) echo "checked"; ?>>
            <img align="absmiddle" style="margin-left: 4px;" src="http://www.webpageanalyse.com/assets/common/theme/images/widgets/certificate-<?php echo $style; ?>.png" alt="<?php echo $this->i18n->_("form.example"); ?>" /><br>
        <?php
        }
        ?>
        </p>
    <?php
    }

    public function uninstall() {
        delete_option('webpageanalyse_certificate_widget_domain');
        delete_option('webpageanalyse_certificate_widget_style');
    }

    private function getTargetDomain() {
        $domain = get_option("webpageanalyse_certificate_widget_domain");
        if (empty($domain)) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $domain = $_SERVER['HTTP_HOST'];
            }
        }
        return $domain;
    }

    private function getStyle() {
        $style = get_option("webpageanalyse_certificate_widget_style");
        if (empty($style))
            return "wl";

        return $style;
    }

    private function getWidgetPath() {
        $style = get_option("webpageanalyse_certificate_widget_style");
        if (empty($style))
            return "certificatew";

        switch ($style) {
            case "wl": return "certificatew";
            case "ws": return "certificatews";
            case "l": return "certificate";
            case "s": return "certificates";
        }
    }

}

class webpageanalyse_certificate_widget_i18n {
    private $lang;
    private $texts = array(
        "de" => array(
            "name" => "WebPageAnalyse Certificate"
            , "domain" => "de.webpageanalyse.com"
            , "link_title" => "Zertifikat für %s auf WebPageAnalyse"
            , "form.your_domain" => "Ihre Domain:"
            , "form.style" => "Stil:"
            , "form.example" => "Beispielwidget"
        ),
        "en" => array(
            "name" => "WebPageAnalyse Certificate"
            , "domain" => "www.webpageanalyse.com"
            , "link_title" => "Certificate for %s on WebPageAnalyse"
            , "form.your_domain" => "Your domain:"
            , "form.style" => "Style:"
            , "form.example" => "Example widget"
        )
    );

    public function __construct($lang) {
        $this->lang = $lang;
    }

    public function _($key) {
        $args = func_get_args();
        if (sizeof($args) > 1) {
            array_shift($args);
            return vsprintf($this->texts[$this->lang][$key], $args);
        } else {
            return $this->texts[$this->lang][$key];
        }
    }
}

$webpageanalyse_certificate_widget = new webpageanalyse_certificate_widget();

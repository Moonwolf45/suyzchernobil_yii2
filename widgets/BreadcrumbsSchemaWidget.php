<?php

namespace app\widgets;


use yii\base\InvalidConfigException;
use yii\bootstrap5\BootstrapWidgetTrait;
use yii\widgets\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class BreadcrumbsSchemaWidget extends Breadcrumbs {
    use BootstrapWidgetTrait;

    public $tag = 'ol';

    public $homeLinkUrl = null;

    public array $navOptions = ['aria' => ['label' => 'breadcrumb']];

    public $itemTemplate = "<li class=\"breadcrumb-item\" itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\">{link}{position}</li>\n";

    /**
     * {@inheritDoc}
     */
    public $activeItemTemplate = "<li class=\"breadcrumb-item active\" itemprop=\"itemListElement\" itemscope itemtype=\"https://schema.org/ListItem\" aria-current=\"page\">{link}{position}</li>\n";

    public int $itempropPosition = 0;

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run(): string {
        if (empty($this->links)) {
            return '';
        }

        if ($this->homeLinkUrl === null) {
            $this->homeLinkUrl = Url::home();
        }

        $links = [];
        foreach ($this->links as $link) {
            if (!is_array($link)) {
                $link = ['label' => $link];
            }

            if (empty($links)) {
                $links[] = $this->renderItemMarkup(
                    [
                        'label' => '<i class="fas fa-home"></i>',
                        'encode' => false,
                        'url' => $this->homeLinkUrl
                    ],
                    "<li class=\"breadcrumb-item\">{link}</li>\n"
                );
            }

            $links[] = $this->renderItemMarkup(
                $link,
                isset($link['url']) ? $this->itemTemplate : $this->activeItemTemplate,
                ++$this->itempropPosition
            );
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = "{$this->getId()}-breadcrumb";
        }
        Html::addCssClass($this->options, ['widget' => 'breadcrumb']);

        return Html::tag('nav', Html::tag(
            $this->tag,
            implode('', $links),
            array_merge(
                $this->options,
                ["itemscope itemtype" => "https://schema.org/BreadcrumbList"],
            )
        ), $this->navOptions);
    }

    /**
     * @param $link
     * @param $template
     * @param int $position
     *
     * @return string
     * @throws InvalidConfigException
     */
    protected function renderItemMarkup($link, $template, int $position = 0): string {
        $encodeLabel = ArrayHelper::remove($link, 'encode', $this->encodeLabels);
        if (array_key_exists('label', $link)) {
            $label = Html::tag('span', $encodeLabel ? Html::encode($link['label']) : $link['label'], ['itemprop' => "name"]);
        } else {
            throw new InvalidConfigException('The "label" element is required for each link.');
        }

        if (isset($link['template'])) {
            $template = $link['template'];
        }

        if (isset($link['url'])) {
            $options = $link;
            unset($options['template'], $options['label'], $options['url']);
            $link = Html::a($label, $link['url'], array_merge($options, ["itemprop" => "item"]));
        } else {
            $link = Html::tag('span', $label, ["itemprop" => "item"]);
        }

        return strtr($template, [
            '{link}' => $link,
            '{position}' => Html::tag('meta', '', ["itemprop" => "position", "content" => $position])
        ]);
    }
}
<?php

use yii\apidoc\renderers\BaseRenderer;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

/* @var $this yii\web\View */

\yii\apidoc\templates\bootstrap\assets\AssetBundle::register($this);

// Navbar hides initial content when jumping to in-page anchor
// https://github.com/twbs/bootstrap/issues/1768
$this->registerJs(<<<JS
    var shiftWindow = function () { scrollBy(0, -50) };
    if (location.hash) shiftWindow();
    window.addEventListener("hashchange", shiftWindow);
JS
,
    \yii\web\View::POS_READY
);

$this->beginPage();
Yii::$app->language = 'zh-CN';
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="language" content="en" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <title><?= Html::encode($this->context->pageTitle) ?></title>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-47725881-2', 'auto');
        ga('send', 'pageview');
</script><!--Google Analytics-->
</head>
<body>

<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Yii 2.0 API 文档',//$this->context->pageTitle,
        'brandUrl' => ($this->context->apiUrl === null && $this->context->guideUrl !== null) ? './guide-index.html' : './index.html',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
        'renderInnerContainer' => false,
        'view' => $this,
    ]);
    $nav = [];

    if ($this->context->apiUrl !== null) {
        $nav[] = ['label' => '类参考', 'url' => rtrim($this->context->apiUrl, '/') . '/index.html'];
        if (!empty($this->context->extensions)) {
            $extItems = [];
            foreach ($this->context->extensions as $ext) {
                $extItems[] = [
                    'label' => $ext,
                    'url' => "./ext-{$ext}-index.html",
                ];
            }
            $nav[] = ['label' => '扩展', 'items' => $extItems];
        }
    }

    if ($this->context->guideUrl !== null) {
        $nav[] = ['label' => '权威指南', 'url' => rtrim($this->context->guideUrl, '/') . '/' . BaseRenderer::GUIDE_PREFIX . 'README.html'];
    }

    //add by woody  08/02/2014
    $githubItems = [
    	[
    		'label' => 'Yii 2.0',
    		'url' => 'https://github.com/yiisoft/yii2',
    	],
    	[
    		'label' => 'Yii 2.0 中文化项目',
    		'url' => 'https://github.com/yii2-chinesization/yii2-zh-cn',
    	],
    	[
    		'label' => 'docwithcn.com',
    		'url' => 'https://github.com/AbrahamGreyson/abrahamgreyson.github.io',
    	],
		[
    		'label' => '下载 PDF',
    		'url' => 'https://github.com/AbrahamGreyson/abrahamgreyson.github.io/download/guide.pdf',
    	]
    ];
    $nav[] = ['label' => 'Github', 'items' => $githubItems];
    //$nav[] = ['label' => '中文翻译错误，歧义，排版问题反馈', 'url' => 'https://github.com/yii2-chinesization/yii2-zh-cn/issues/new']
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $nav,
        'view' => $this,
        'params' => [],
    ]);
?>
<div class="navbar-form navbar-left" role="search">
  <div class="form-group">
    <input id="searchbox" type="text" class="form-control" placeholder="Search">
  </div>
</div>
<?php
    \yii\apidoc\templates\bootstrap\assets\JsSearchAsset::register($this);

    // defer loading of the search index: https://developers.google.com/speed/docs/best-practices/payload?csw=1#DeferLoadingJS
    $this->registerJs(<<<JS
var element = document.createElement("script");
element.src = "./jssearch.index.js";
document.body.appendChild(element);
JS
);

    $this->registerJs(<<<JS

var searchBox = $('#searchbox');

// focus the search field
searchBox.focus();

// search when typing in search field
searchBox.on("keyup", function(event) {
    var query = $(this).val();

    if (query == '' || event.which == 27) {
        $('#search-resultbox').hide();
        return;
    } else if (event.which == 13) {
        var selectedLink = $('#search-resultbox a.selected');
        if (selectedLink.length != 0) {
            document.location = selectedLink.attr('href');
            return;
        }
    } else if (event.which == 38 || event.which == 40) {
        $('#search-resultbox').show();

        var selected = $('#search-resultbox a.selected');
        if (selected.length == 0) {
            $('#search-results').find('a').first().addClass('selected');
        } else {
            var next;
            if (event.which == 40) {
                next = selected.parent().next().find('a').first();
            } else {
                next = selected.parent().prev().find('a').first();
            }
            if (next.length != 0) {
                var resultbox = $('#search-results');
                var position = next.position();

//              TODO scrolling is buggy and jumps around
//                resultbox.scrollTop(Math.floor(position.top));
//                console.log(position.top);

                selected.removeClass('selected');
                next.addClass('selected');
            }
        }

        return;
    }
    $('#search-resultbox').show();
    $('#search-results').html('<li><span class="no-results">No results</span></li>');

    var result = jssearch.search(query);

    if (result.length > 0) {
        var i = 0;
        var resHtml = '';

        for (var key in result) {
            if (i++ > 20) {
                break;
            }
            resHtml = resHtml +
            '<li><a href="' + result[key].file.u.substr(3) +'"><span class="title">' + result[key].file.t + '</span>' +
            '<span class="description">' + result[key].file.d + '</span></a></li>';
        }
        $('#search-results').html(resHtml);
    }
});

// hide the search results on ESC
$(document).on("keyup", function(event) { if (event.which == 27) { $('#search-resultbox').hide(); } });
// hide search results on click to document
$(document).bind('click', function (e) { $('#search-resultbox').hide(); });
// except the following:
searchBox.bind('click', function(e) { e.stopPropagation(); });
$('#search-resultbox').bind('click', function(e) { e.stopPropagation(); });

JS
);

    NavBar::end();
    ?>

    <div id="search-resultbox" style="display: none;" class="modal-content">
        <ul id="search-results">
        </ul>
    </div>

    <?= $content ?>

</div>

<footer class="footer">
    <?php /* <p class="pull-left">&copy; My Company <?= date('Y') ?></p> */ ?>
    <p class="pull-right"><small>Page generated on <?= date('r') ?></small></p>
    <?= Yii::powered() ?>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title><?= $project->name; ?> - 统计结果</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .bug_title {
            display: inline-block;
            width: 800px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div>
    <h1 class="text-center"><?= $project->name; ?></h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>BUG</th>
            <th>重复激活次数</th>
            <th>当前状态</th>
            <th>解决方式</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bugs as $bug): ?>
            <tr>
                <td>
                    <a class="bug_title" target="_blank" title="<?= $bug->title; ?>"
                       href="<?= zentao_bug_url($bug->id); ?>"><?= $bug->title; ?></a>
                </td>
                <td><?= $bug->activatedCount; ?></td>
                <td><?= $bug->getFriendlyStatus(); ?></td>
                <td><?= $bug->getFriendlyResolution(); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript" src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>任务列表</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .task_name {
            display: inline-block;
            width: 200px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div>
    <h1 class="text-center">任务列表</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>任务</th>
            <th>预计工时<br>（小时）</th>
            <th>实际工时<br>（小时）</th>
            <th>工时正偏差<br>（小时）</th>
            <th>工时负偏差<br>（小时）</th>
            <th>预计开始日期</th>
            <th>实际开始日期</th>
            <th>预计截至日期</th>
            <th>实际截至日期</th>
            <th>工期正偏差<br>（天）</th>
            <th>工期负偏差<br>（天）</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td>
                    <a class="task_name" target="_blank" title="<?= $task->name; ?>"
                       href="<?= zentao_task_url($task->id); ?>"><?= $task->name; ?></a>
                </td>
                <td><?= $task->estimate; ?></td>
                <td><?= $task->consumed; ?></td>
                <td><?= abs($task->getHourPlusDeviation()); ?></td>
                <td><?= abs($task->getHourMinusDeviation()); ?></td>
                <td><?= $task->estStarted; ?></td>
                <td><?= $task->realStarted; ?></td>
                <td><?= $task->deadline->toDateString(); ?></td>
                <td><?= $task->finishedDate->toDateString(); ?></td>
                <td><?= abs($task->getDayPlusDeviation()); ?></td>
                <td><?= abs($task->getDayMinusDeviation()); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td><strong>总计：</strong></td>
            <td><strong><?= $total['estimate_sum']; ?></strong></td>
            <td><strong><?= $total['consumed_sum']; ?></strong></td>
            <td><strong><?= $total['hour_plus_deviation']; ?></strong></td>
            <td><strong><?= $total['hour_minus_deviation']; ?></strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong><?= $total['day_plus_deviation']; ?></strong></td>
            <td><strong><?= $total['day_minus_deviation']; ?></strong></td>
        </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
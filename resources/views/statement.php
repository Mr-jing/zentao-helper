<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>统计结果</title>
    <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div>
    <h1 class="text-center"> <?=$start;?> 至 <?=$end;?> 统计结果</h1>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>成员名称<br/>（没有顺序）</th>
            <th>预计总工时<br/>（小时）</th>
            <th>实际总工时<br/>（小时）</th>
            <th>工时正偏差<br/>（小时）</th>
            <th>工时负偏差<br/>（小时）</th>
            <th>工期正偏差<br/>（天）</th>
            <th>工期负偏差<br/>（天）</th>
            <th>未解决<br/> Bug 总数</th>
            <th>已解决个人<br/> Bug 总数</th>
            <th>已解决一二级<br/> Bug 数</th>
            <th>已解决反复激活<br/> Bug 数</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $user => $row): ?>
            <tr>
                <td><?= $user; ?></td>
                <td><?= $row['estimate_sum']; ?></td>
                <td><?= $row['consumed_sum']; ?></td>
                <td><?= abs($row['hour_plus_deviation']); ?></td>
                <td><?= abs($row['hour_minus_deviation']); ?></td>
                <td><?= abs($row['day_plus_deviation']); ?></td>
                <td><?= abs($row['day_minus_deviation']); ?></td>
                <td><?= $row['assigned']; ?></td>
                <td><?= $row['resolved']; ?></td>
                <td><?= $row['resolved_severity']; ?></td>
                <td><?= $row['resolved_activated']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript" src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
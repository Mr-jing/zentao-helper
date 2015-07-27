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
    <h1 class="text-center"> <?= $start; ?> 至 <?= $end; ?> 统计结果</h1>

    <div style="width: 500px; margin: 30px auto">
        <form method="GET" action="<?= url('/statement'); ?>">
            <div class="form-group">
                <label for="users">账号：</label>
                <textarea id="users" name="users" class="form-control" rows="5"
                          required><?= implode('|', $users); ?></textarea>
            </div>
            <div class="form-group">
                <label for="start">起始时间：</label>
                <input type="text" id="start" name="start" class="form-control" required value="<?= $start; ?>">
            </div>
            <div class="form-group">
                <label for="end">截止时间：</label>
                <input type="text" id="end" name="end" class="form-control" required value="<?= $end; ?>">
            </div>
            <button class="btn btn-lg btn-success btn-block" type="submit">查询</button>
        </form>
    </div>

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
                <td>
                    <a href="<?= url('/task/deviations?' . http_build_query(array(
                            'status' => 'done',
                            'finishedBy' => $user,
                            'start' => $start,
                            'end' => $end,
                        ))); ?>">
                        <strong><?= $user; ?></strong>
                    </a>
                </td>
                <td><?= $row['estimate_sum']; ?></td>
                <td><?= $row['consumed_sum']; ?></td>
                <td><?= abs($row['hour_plus_deviation']); ?></td>
                <td><?= abs($row['hour_minus_deviation']); ?></td>
                <td><?= abs($row['day_plus_deviation']); ?></td>
                <td><?= abs($row['day_minus_deviation']); ?></td>
                <td>
                    <a href="<?= url('/bug/search?' . http_build_query(array(
                            'assignedTo' => $user,
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                        ))); ?>">
                        <strong><?= $row['assigned']; ?></strong>
                    </a>
                </td>
                <td>
                    <a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                        ))); ?>">
                        <strong><?= $row['resolved']; ?></strong>
                    </a>
                    (<a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                            'resolution' => implode('|', \App\Bug::$efficientResolution),
                        ))); ?>">
                        <strong><?= $row['efficient_resolved']; ?></strong>
                    </a>)
                </td>
                <td>
                    <a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'severity' => implode('|', \App\Bug::$efficientSeverity),
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                        ))); ?>">
                        <strong><?= $row['resolved_severity']; ?></strong>
                    </a>
                    (<a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'severity' => implode('|', \App\Bug::$efficientSeverity),
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                            'resolution' => implode('|', \App\Bug::$efficientResolution),
                        ))); ?>">
                        <strong><?= $row['efficient_resolved_severity']; ?></strong>
                    </a>)
                </td>
                <td>
                    <a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'reactivated' => 1,
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                        ))); ?>">
                        <strong><?= $row['resolved_activated']; ?></strong>
                    </a>
                    (<a href="<?= url('/bug/search?' . http_build_query(array(
                            'resolvedBy' => $user,
                            'reactivated' => 1,
                            'openedDateStart' => $start,
                            'openedDateEnd' => $end,
                            'resolution' => implode('|', \App\Bug::$efficientResolution),
                        ))); ?>">
                        <strong><?= $row['efficient_resolved_activated']; ?></strong>
                    </a>)
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript" src="http://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.bootcss.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>
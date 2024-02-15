<div class="container card">
    <table class="table">
        <tr>
            <td><b>Title</b></td>
            <td>:</td>
            <td>{{ $task->title }}</td>
        </tr>
        <tr>
            <td><b>Description</b></td>
            <td>:</td>
            <td>{{ $task->description }}</td>
        </tr>
        <tr>
            <td><b>Priority</b></td>
            <td>:</td>
            <?php
                if ($task->priority == '0') {
                    $priority = 'Low';
                }
                elseif ($task->priority == '1') {
                    $priority = 'Medium';
                }
                else {
                    $priority = 'High';
                }
            ?>
            <td>{{ $priority }}</td>
        </tr>
        <tr>
            <td><b>Status</b></td>
            <td>:</td>
            <td>{{ ($task->completed == '0') ? 'Incompleted' : 'completed' }}</td>
        </tr>
    </table>
</div>
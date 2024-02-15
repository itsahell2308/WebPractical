@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-4" id="todo">
            <h3>Low Task</h3>
            <ul class="list-group" id="low-task" data-priority="0">
                @foreach ($low_task as $task)
                    <li class="list-group-item" data-task-id="{{ $task->id }}">{{ $task->title }}</li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-4" id="in-progress">
            <h3>Medium Task</h3>
            <ul class="list-group" id="med-task" data-priority="1">
                @foreach ($med_task as $task)
                    <li class="list-group-item" data-task-id="{{ $task->id }}">{{ $task->title }}</li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-4" id="done">
            <h3>High Task</h3>
            <ul class="list-group" id="high-task" data-priority="2">
                @foreach ($high_task as $task)
                    <li class="list-group-item" data-task-id="{{ $task->id }}">{{ $task->title }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function() {
            var droppableContainer;

            $("#low-task").on("mouseover", "li", function() {
                $(this).draggable({
                    helper: "clone",
                    revert: "invalid"
                });
            });
            $("#med-task").on("mouseover", "li", function() {
                $(this).draggable({
                    helper: "clone",
                    revert: "invalid"
                });
            });
            $("#high-task").on("mouseover", "li", function() {
                $(this).draggable({
                    helper: "clone",
                    revert: "invalid"
                });
            });

            // Make the in-progress and done lists droppable
            $("#low-task, #med-task, #high-task").droppable({
                accept: "#low-task li, #med-task li, #high-task li",
                drop: function(event, ui) {
                    // Handle the drop event
                    var taskId = ui.helper.data('task-id');
                    var newStatus = $(this).data('priority');
                    droppableContainer = $(this);  // Update the variable with the current droppable container

                    console.log("newStatus", newStatus);
                    console.log("taskId", taskId);

                    // Make an AJAX request to update the task status
                    $.ajax({
                        url: "{{ route('user.task.priority.change') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        data: {
                            id: taskId,
                            priority: newStatus,
                        },
                        success: function(response) {
                            // Update the UI as needed
                            ui.helper.appendTo(droppableContainer.find("ul"));
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                }
            });
        });
    </script>
@endsection

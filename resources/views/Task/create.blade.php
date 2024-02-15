<form action="{{ route('user.task.store') }}" method="post" id="task-form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ isset($task) ? $task->id : '' }}">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title"
                value="{{ isset($task) ? $task->title : '' }}">
            <span class="text-danger error" id="titleerror"></span>
        </div>
        <div class="col-lg-12 mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" cols="10" rows="4"
                placeholder="Enter Description">{{ isset($task) ? $task->description : '' }}</textarea>
            <span class="text-danger error" id="descriptionerror"></span>
        </div>
        <div class="col-lg-12 mb-3">
            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
            <select class="form-control" name="priority" id="priority">
                <option selected disabled>select</option>
                <option value="0" @if (isset($task) && $task->priority == '0') selected @endif>Low</option>
                <option value="1" @if (isset($task) && $task->priority == '1') selected @endif>Medium</option>
                <option value="2" @if (isset($task) && $task->priority == '2') selected @endif>High</option>
            </select>
            <span class="text-danger error" id="priorityerror"></span>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="completed" name="completed"
                    value="1" @if (isset($task) && $task->completed == '1') checked @endif>
                <label class="form-check-label" for="completed">Completed</label>
            </div>
        </div>
        <div class="col-lg-12 mb-3">
            <label for="file" class="form-label">Image </label>
            <input class="form-control" type="file" id="file" name="file" accept="image/png, image/gif, image/jpeg">
            <span class="text-danger error" id="fileerror"></span>
        </div>
        <div class="col-lg-12 mb-3 d-flex justify-content-end ">
            <button class="btn btn-primary" type="submit" id="task-submit">Submit</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // $('#priority').select2({
        //     placeholder: 'Select an Option',
        // });
    })
</script>
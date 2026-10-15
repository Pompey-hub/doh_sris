<?php if (!empty(validation_errors())): ?>
    <div class="alert alert-danger">
        <a class="close" data-dismiss="alert" title="close">x</a>
        <ul><?php echo (validation_errors('<li>', '</li>')); ?></ul>
    </div>
<?php endif; ?>

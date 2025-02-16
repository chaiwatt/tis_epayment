

  <!-- Modal -->
  <div class="modal fade" id="HistoryModal<?php echo e($history->id); ?>" tabindex="-1" role="dialog" aria-labelledby="ReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" > 
                <?php echo e($history->DataSystem ?? null); ?>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
           </h4>
        </div>
        <div class="modal-body text-left">
            <div class="container-fluid">
                <?php if(!is_null($history)): ?>
                <?php if($history->system == 1): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system01', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 2): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system02', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 3): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system03', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 4): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system04', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 5): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system05', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 6): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system06', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 8 || $history->system == 9 || $history->system == 10): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system08', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 11): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system11', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php elseif($history->system == 12): ?>
                    <?php echo $__env->make('certify/check_certificate_lab/history.system12', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
            <?php else: ?> 
            <?php endif; ?>
            </div>
        </div>
      </div>
    </div>
</div>


<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);


$this->addExternalCss("//cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css");
$this->addExternalJS("//cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js");
?>

<div class="pt-5 text-center">
  <img class="d-block mx-auto mb-4" src="//getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
</div>

<div class="row">
  <div class="col-12">
    <form id="aj-form" class="was-validated">

      <div class="pb-5 text-center">
        <h2><?=Loc::GetMessage("L151_FEEDBACK_TEMPLATE_MAIN_TEXT")?></h2>
      </div>

      <div class="row">
        <div class="col-6">
          <input type="text" class="form-control" name="name" id="aj-form-name" placeholder="<?=Loc::GetMessage("L151_FEEDBACK_TEMPLATE_NAME")?>" value="" required>
        </div>

        <div class="col-6">
          <input type="email" class="form-control" name="email" id="aj-form-email" aria-describedby="validation-aj-form-email" placeholder="username@mail.ru" required>
        </div>
            
        <div class="mb-3 pt-3">
          <legend class="form-text"><?=Loc::GetMessage("L151_FEEDBACK_TEMPLATE_RATING")?>:</legend>
          <div class="form-check form-check-inline">
            <input name="rating" type="radio" class="form-check-input aj-form-rating" value="1" required>
            <label class="form-check-label" for="credit">1</label>
          </div>
          <div class="form-check form-check-inline">
            <input name="rating" type="radio" class="form-check-input aj-form-rating" value="2" required>
            <label class="form-check-label" for="credit">2</label>
          </div>
          <div class="form-check form-check-inline">
            <input name="rating" type="radio" class="form-check-input aj-form-rating" value="3" required>
            <label class="form-check-label" for="credit">3</label>
          </div>
          <div class="form-check form-check-inline">
            <input name="rating" type="radio" class="form-check-input aj-form-rating" value="4" required>
            <label class="form-check-label" for="credit">4</label>
          </div>
          <div class="form-check form-check-inline">
            <input name="rating" type="radio" class="form-check-input aj-form-rating" value="5" required>
            <label class="form-check-label" for="credit">5</label>
          </div>
        </div>

          <div class="mb-3">
            <textarea class="form-control" name="comment" id="aj-form-comment" placeholder="<?=Loc::GetMessage("L151_FEEDBACK_TEMPLATE_COMMENT")?>" maxlength="500" required></textarea>
          </div>
      </div>
          
      <hr class="my-4">

      <input type="hidden" id="aj-form-detected" name="detected" value="">

      <button id="aj-form-submit" class="w-100 btn btn-primary btn-lg" type="submit"><?=Loc::GetMessage("L151_FEEDBACK_TEMPLATE_SUBMIT")?></button>

    </form>
  </div>
</div>

<script>
  //
  var ajFormParams = <?=\Bitrix\Main\Web\Json::encode([
    "componentName"     => $this->getComponent()->getName(),
    "signedParameters"  => $this->getComponent()->getSignedParameters(),
  ])?>;
</script>
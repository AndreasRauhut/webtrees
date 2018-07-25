<?php use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Http\Controllers\StatisticsChartController;
use Fisharebest\Webtrees\I18N; ?>
<?php ?>
<?php ?>

<script>
  function statusDisable (sel) {
    var cbox = document.getElementById(sel);
    cbox.checked = false;
    cbox.disabled = true;
  }

  function statusEnable (sel) {
    var cbox = document.getElementById(sel);
    cbox.disabled = false;
  }

  function statusHide(sel) {
    var box           = document.getElementById(sel);
    box.style.display = "none";
    var box_m         = document.getElementById(sel + "_m");
    if (box_m) {
      box_m.style.display = "none";
    }
    if (sel === "map_opt") {
      var box_axes = document.getElementById("axes");
      if (box_axes) {
        box_axes.style.display = "";
      }
      var box_zyaxes = document.getElementById("zyaxes");
      if (box_zyaxes) {
        box_zyaxes.style.display = "";
      }
    }
  }

  function statusShow(sel) {
    var box           = document.getElementById(sel);
    box.style.display = "";
    var box_m         = document.getElementById(sel + "_m");
    if (box_m) {
      box_m.style.display = "none";
    }
    if (sel === "map_opt") {
      var box_axes = document.getElementById("axes");
      if (box_axes) {
        box_axes.style.display = "none";
      }
      var box_zyaxes = document.getElementById("zyaxes");
      if (box_zyaxes) {
        box_zyaxes.style.display = "none";
      }
    }
  }

  function statusChecked (sel) {
    var cbox = document.getElementById(sel);
    cbox.checked = true;
  }

  function statusShowSurname(x) {
    if (x.value === "surname_distribution_chart") {
      document.getElementById("surname_opt").style.display = "";
    } else if (x.value !== "surname_distribution_chart") {
      document.getElementById("surname_opt").style.display = "none";
    }
  }

  function loadCustomChart() {
    $("#custom-chart").html("");
    var form = $("#own-stats-form");
    jQuery.get(form.attr("action"), form.serialize())
      .done(function (data) {
        $("#custom-chart").html(data);
      })
      .fail(function (jqXHR, textStatus) {
        // Server error?  Show something to get rid of the spinner.
        $("#custom-chart").html(textStatus);
      });
    return false;
  }
</script>

<h3>
    <?= I18N::translate('Create your own chart') ?>
</h3>

<form id="own-stats-form" action="<?= e(route('statistics-chart')) ?>" onsubmit="return loadCustomChart();" class="wt-page-options wt-page-options-statistics">
    <input type="hidden" name="ged" value="<?= e($tree->getName()) ?>">
    <div class="form-group row">
        <div class="col-sm-2 wt-page-options-label">
            <?= I18N::translate('Chart type') ?>
        </div>
        <div class="col-sm-4 wt-page-options-value">
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_BIRTH_MONTH => I18N::translate('Month of birth')], StatisticsChartController::X_AXIS_BIRTH_MONTH, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_DEATH_MONTH => I18N::translate('Month of death')], '', false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [
                StatisticsChartController::X_AXIS_MARRIAGE_MONTH       => I18N::translate('Month of marriage'),
                StatisticsChartController::X_AXIS_FIRST_MARRIAGE_MONTH => I18N::translate('Month of first marriage'),
            ], '', false, ['onchange' => 'statusChecked("z_none"); statusDisable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_FIRST_CHILD_MONTH => I18N::translate('Month of birth of first child in a relation')], '', false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_AGE_AT_DEATH => I18N::translate('Average age at death')], '', false, ['onchange' => 'statusEnable("z_sex"); statusShow("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_AGE_AT_MARRIAGE => I18N::translate('Age in year of marriage')], '', false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusShow("x_years_m"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_AGE_AT_FIRST_MARRIAGE => I18N::translate('Age in year of first marriage')], '', false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusShow("x_years_m"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_NUMBER_OF_CHILDREN => I18N::translate('Number of children')], '', false, ['onchange' => 'statusChecked("z_none"); statusDisable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("map_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_INDIVIDUAL_MAP => I18N::translate('Individual distribution')], '', false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusShow("map_opt"); statusShow("chart_type"); statusHide("axes");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_BIRTH_MAP => I18N::translate('Birth by country')], '', false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_MARRIAGE_MAP => I18N::translate('Marriage by country')], '', false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>
            <?= Bootstrap4::radioButtons('x-as', [StatisticsChartController::X_AXIS_DEATH_MAP => I18N::translate('Death by country')], '', false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>

            <div id="x_years" style="display:none;">
                <label for="x-axis-boundaries-ages">
                    <?= I18N::translate('Select the desired age interval') ?>
                </label>
                <br>
                <?= Bootstrap4::select([
                    '1,5,10,20,30,40,50,60,70,80,90,100' => I18N::plural('%s year', '%s years', 10, I18N::number(10)),
                    '5,20,40,60,75,80,85,90'             => I18N::plural('%s year', '%s years', 20, I18N::number(20)),
                    '10,25,50,75,100'                    => I18N::plural('%s year', '%s years', 25, I18N::number(25)),
                ], '1,5,10,20,30,40,50,60,70,80,90,100', [
                    'id'   => 'x-axis-boundaries-ages',
                    'name' => 'x-axis-boundaries-ages',
                ]) ?>
            </div>

            <div id="x_years_m" style="display:none;">
                <label for="x-axis-boundaries-ages_m">
                    <?= I18N::translate('Select the desired age interval') ?>
                </label>
                <?= Bootstrap4::select([
                    '16,18,20,22,24,26,28,30,32,35,40,50' => I18N::plural('%s year', '%s years', 2, I18N::number(2)),
                    '20,25,30,35,40,45,50'                => I18N::plural('%s year', '%s years', 5, I18N::number(5)),
                ], '16,18,20,22,24,26,28,30,32,35,40,50', [
                    'id'   => 'x-axis-boundaries-ages_m',
                    'name' => 'x-axis-boundaries-ages_m',
                ]) ?>
            </div>

            <div id="x_numbers" style="display:none;">
                <label for="x-axis-boundaries-numbers">
                    <?= I18N::translate('Select the desired count interval') ?>
                </label>
                <br>
                <select id="x-axis-boundaries-numbers" name="x-axis-boundaries-numbers">
                    <option value="1,2,3,4,5,6,7,8,9,10" selected>
                        <?= I18N::translate('interval one child') ?>
                    </option>
                    <option value="2,4,6,8,10,12">
                        <?= I18N::translate('interval two children') ?>
                    </option>
                </select>
                <br>
            </div>
            <div id="map_opt" style="display:none;">
                <div id="chart_type">
                    <label>
                        <?= I18N::translate('Chart type') ?>
                        <br>
                        <select name="chart_type" onchange="statusShowSurname(this);">
                            <option value="indi_distribution_chart" selected>
                                <?= I18N::translate('Individual distribution chart') ?>
                            </option>
                            <option value="surname_distribution_chart">
                                <?= I18N::translate('Surname distribution chart') ?>
                            </option>
                        </select>
                    </label>
                    <br>
                </div>
                <div id="surname_opt" style="display:none;">
                    <label for="SURN">
                        <?= I18N::translate('Surname') ?>
                    </label>
                    <br>
                    <input data-autocomplete-type="SURN" type="text" id="SURN" name="SURN" size="20">
                    <br>
                </div>
                <label for="chart_shows">
                    <?= I18N::translate('Geographic area') ?>
                </label>
                <br>
                <select id="chart_shows" name="chart_shows">
                    <option value="world" selected>
                        <?= I18N::translate('World') ?>
                    </option>
                    <option value="europe">
                        <?= I18N::translate('Europe') ?>
                    </option>
                    <option value="usa">
                        <?= I18N::translate('United States') ?>
                    </option>
                    <option value="south_america">
                        <?= I18N::translate('South America') ?>
                    </option>
                    <option value="asia">
                        <?= I18N::translate('Asia') ?>
                    </option>
                    <option value="middle_east">
                        <?= I18N::translate('Middle East') ?>
                    </option>
                    <option value="africa">
                        <?= I18N::translate('Africa') ?>
                    </option>
                </select>
            </div>
        </div>
        <div class="col-sm-2 wt-page-options-label" id="axes">
            <?= I18N::translate('Categories') ?>
        </div>
        <div class="col-sm-4 wt-page-options-value" id="zyaxes">
            <label>
                <input type="radio" id="z_none" name="z-as" value="<?= StatisticsChartController::Z_AXIS_ALL ?>" onclick="statusDisable('z-axis-boundaries-periods');">
                <?= I18N::translate('none') ?>
            </label>
            <br>
            <label>
                <input type="radio" id="z_sex" name="z-as" value="<?= StatisticsChartController::Z_AXIS_SEX ?>" onclick="statusDisable('z-axis-boundaries-periods');">
                <?= I18N::translate('gender') ?>
            </label>
            <br>
            <label>
                <input type="radio" id="z_time" name="z-as" value="<?= StatisticsChartController::Z_AXIS_TIME ?>" checked onclick="statusEnable('z-axis-boundaries-periods');">
                <?= I18N::translate('date periods') ?>
            </label>
            <br>
            <br>
            <label for="z-axis-boundaries-periods">
                <?= I18N::translate('Date range') ?>
            </label>
            <br>
            <select id="z-axis-boundaries-periods" name="z-axis-boundaries-periods">
                <option value="1700,1750,1800,1850,1900,1950,2000" selected>
                    <?= /* I18N: from 1700 interval 50 years */
                    I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1700), I18N::number(50)) ?>
                </option>
                <option value="1800,1840,1880,1920,1950,1970,2000">
                    <?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 40, I18N::digits(1800), I18N::number(40)) ?>
                </option>
                <option value="1800,1850,1900,1950,2000">
                    <?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1800), I18N::number(50)) ?>
                </option>
                <option value="1900,1920,1940,1960,1980,1990,2000">
                    <?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 20, I18N::digits(1900), I18N::number(20)) ?>
                </option>
                <option value="1900,1925,1950,1975,2000">
                    <?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 25, I18N::digits(1900), I18N::number(25)) ?>
                </option>
                <option value="1940,1950,1960,1970,1980,1990,2000">
                    <?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 10, I18N::digits(1940), I18N::number(10)) ?>
                </option>
            </select>
            <br>
            <br>
            <?= I18N::translate('Results') ?>
            <br>
            <label>
                <input type="radio" name="y-as" value="<?= StatisticsChartController::Y_AXIS_NUMBERS ?>" checked>
                <?= I18N::translate('numbers') ?>
            </label>
            <br>
            <label>
                <input type="radio" name="y-as" value="<?= StatisticsChartController::Y_AXIS_PERCENT ?>">
                <?= I18N::translate('percentage') ?>
            </label>
            <br>
        </div>
    </div>
    <p class="center">
        <button type="submit" class="btn btn-primary">
            <?= I18N::translate('show the chart') ?>
        </button>
    </p>
</form>

<div id="custom-chart" class="wt-ajax-load">
    <!-- Not initially empty, to disable spinner -->
</div>

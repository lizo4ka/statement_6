<?php

namespace app\modules\glr\models;

use yii\helpers\Html;


$this->title = 'Форма ГЛР 1.6';
$this->params['breadcrumbs'][] = $this->title;
?>


<style>
    <!--
    .statementGlr16 {
        font-family: "Times New Roman", serif;
        font-size: 10pt
    }

    .docHeader {margin: 0 0 20pt 20cm; width: 7sm}
    .docHeader .oivName {padding-top: 12pt}
    .docHeader .formnum {font-size: 10pt; text-align: right}

    .docFooter {margin: 20pt 20pt 0 309pt}
    .docFooter p {text-transform:uppercase}

    p {margin: 0; font-size: 12pt}
    .sub {font-size: 10pt; text-align: center; padding: 1pt 0 0 0; border-top: solid}
    .sub.address_district{margin-left: 140pt}
    .sub.address_municip{margin-left: 168pt}
    .sub.address_subject {margin-left:277pt;}
    .sub.address_forestry {margin-left: 161pt;}
    .sub.address_category {margin-left: 415pt;}
    .statementGlr16 td {vertical-align: top;}
    .addressPart {margin: 6pt 0 12pt}

    p.anketa {margin:12pt 0 6pt 0;}
    table.statement {border-collapse: collapse; border: solid 1pt; font-size: 9pt}
    table.statement td {text-align:center}
    table.statement .colnums td {font-size: 7pt; color: #999}
    table.statement td {border: solid 1pt; padding: 0 1.4pt 0 1.4pt;}
    div.vertical {writing-mode: tb-rl; filter: flipH flipV; -webkit-transform: rotate(180deg); -moz-transform: rotate(180deg); -o-transform: rotate(180deg); -ms-transform: rotate(180deg); transform: rotate(180deg); margin-top:5pt; margin-bottom: 5pt}
    .note_beforetable {font-size: 9pt; text-align: right;}
    .note_aftertable {font-size: 9pt}

    .signature p:nth-child(1) {margin-top:6pt}
    .signature p:nth-child(2) {margin-top: 12pt; padding-left: 30pt}
    .signature span:nth-child(1) {}
    .signature span:nth-child(2) {padding-left: 130pt}
    .signature span:nth-child(3) {padding-left: 130pt}
    .signature .sub {margin: 0 260pt 0 30pt}

    -->
</style>



<?php

function formatter($num) {
    return number_format( $num / 10000, 4, ',', ' ');
}

function formatter2($num) {
    return number_format( $num / 10000, 1, ',', ' ');
}

// Разбор адресной части и перечисление выделов, если есть данные по пересечке

if($intersectedStrips) {

    foreach ($intersectedStrips as $arritem) {

        // **** Контроль входных данных для обработки запросов ***
        // Если все необходимые данные присутствуют - разбираем массив на адресность
        if (isset($arritem->sri) &&
        isset($arritem->mu) &&
        isset($arritem->gir) &&
        isset($arritem->kv) &&
        isset($arritem->sknr) )
        {

        //     // Выводим адресную часть всех выделов, попавших под пересечку
        //     // сгруппированную по субъектам, лесничествам, уч. л-вам и кварталам

        //     //*** СУБЪЕКТЫ РФ ***
            if (!isset($sri) || $sri != $arritem->sri) {

                $sri = $arritem->sri;
                $fedSub = FederalSubject::find()->where(['federal_subject_id' => $sri])->one();

                // Наименование ОИВ
                $oiv = (OivSubject::find()->where(['fed_subject' => $sri])->one() )->name;
                $numoiv = (OivSubject::find()->where(['fed_subject' => $sri])->one() )->id;
            }

            //*** ЛЕСНИЧЕСТВА ***
            if (!isset($mu) || $mu != $arritem->mu) {

            $mu = $arritem->mu;
            $frstry = Forestry::find()->where(['KOD_SUB' => $sri, 'KOD_LN' => $mu])->one();




?>

<!-- Для каждого лесничества рисуем отдельную выписку -->
<div class="statementGlr16">

    <div class="docHeader">
        <p class="formnum">Форма 1.6.</p>
       <p class="oivName"><?= $oiv ?></p>
        <p class="sub">(наименование органа государственной власти)</p>
    </div>

    <p style='text-align:center; margin-bottom:12pt;'>
        <b>ВЫПИСКА ИЗ ГОСУДАРСТВЕННОГО ЛЕСНОГО РЕЕСТРА № <?= $orderId ?>
            <br>О количественных, качественных и экономических характеристиках лесов и лесных ресурсов<br> на <?php $w = date('w'); //порядковый номер недели
            if ($w < 6)
                //считаем разницу дней: общее кол-во дней в неделю (7) - пятница (5) + текущий день недели 
                $def = 2 + $w; 
            else
                $def = 1; //если текущий день сб, то разница в днях равна 1 
            echo  date('d.m.Y', strtotime("-$def day")); //выводим дату прошлой пт ?> г.</b>
    </p>

    <p>
        <br>Наименование субъекта Российской Федерации &nbsp;&nbsp;&nbsp;
        <?= (FederalSubject::find()->where(['federal_subject_id' => $sri ])->one())->name ?>
    </p>

    <p class="sub address_subject">&nbsp;</p>

    <p>Наименование категории земель, на которой расположено лесничество</p> 

    <p class="sub address_category">&nbsp;</p>

    <p>Муниципальное образование</p>

    <p class="sub address_municip">&nbsp;</p>

    <p>Наименование лесничества &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= (Forestry::find()->where(['KOD_SUB' =>$sri, 'KOD_LN' => $mu])->one())->LN_NAME ?></p>

    <p class="sub address_forestry">&nbsp;</p>

    <p>Участковое лесничество &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= (SubforestryFf::find()->where(['subject_kod' => $sri, 'forestry_kod' => $mu, 'subforestry_kod' => $arritem->gir])->one()->subforestry_name) ?>
     </p>

    <p class="sub address_district">&nbsp;</p>


    <table class="statement" cellspacing=0 cellpadding=0>
        <thead>
        <tr>
            <td rowspan="3">№ квартала</td>
            <td rowspan="3">№ выдела</td>
            <td rowspan="3">Площадь, га</td>
            <td rowspan="3">Характерис- тика лесов</td>
            <td rowspan="3"><div class="vertical">Ярус</td>
            <td rowspan="3"><div class="vertical">Высота яруса</div></td>
            <td rowspan="3"><div class="vertical">Коэф- фициент состава</td>
            <td rowspan="3"><div class="vertical">Порода</td>
            <td rowspan="3"><div class="vertical">Воз- раст</td>
            <td rowspan="3"><div class="vertical">Высота</td>
            <td rowspan="3"><div class="vertical">Диаметр</td>
            <td rowspan="3">Класс воз- раста</td>
            <td rowspan="3">Групаа воз- раста</td>
            <td rowspan="3">Бонитет</td>
            <td rowspan="3">Тип леса ТЛУ</td>
            <td rowspan="3">Полнота</td>
            <td rowspan="3">Запас на 1 га</td>
            <td colspan="2">Запас сырорастущего леса на выделе, дес. м<sup>3</sup> </td>
            <td rowspan="3">Класс товар- ности</td>
            <td colspan="4">Запас на выделе, кбм</td>
            <td rowspan="3">Хозяйственные мероприятия</td>
            <td rowspan="3">Дополнительные сведения</td>
        </tr>
        <tr>
            <td rowspan="2">общий</td>
            <td rowspan="2">в т. ч. по состав. породам</td>
            <td rowspan="2">сухостоя (старого)</td>
            <td rowspan="2">редин (еди- ничные деревья)</td>
            <td colspan="2">Захламленность</td>
        </tr>
        <tr>
            <td>общая</td>
            <td>ликвида</td>
        </tr>
        </thead>

        <tr class="colnums">
            <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
            <td>6</td><td>7</td><td>8</td><td>9</td><td>10</td>
            <td>11</td><td>12</td><td>13</td><td>14</td><td>15</td>
            <td>16</td><td>17</td><td>18</td><td>19</td><td>20</td>
            <td>21</td><td>22</td><td>23</td><td>24</td><td>25</td><td>26</td>
        </tr>
            <?php

            }



            //*** УЧАСТКОВЫЕ ЛЕСНИЧЕСТВА ***
            if (!isset($gir) || $gir != $arritem->gir) {

                // Если изменился номер участкового лесничества (существует прежний номер) - печатаем строку с данными
                if(isset($gir)) {
                
                    $start_count = 0;
                    // printRow();


                }

                

                $start_count = 1; // начали рассчеты по участковому лесничеству: флаг


                $gir = $arritem->gir;

                //$subfrstry = Subforestry::find()->where(['KOD_SUB' => $sri, 'KOD_LN' => $mu, 'KOD_ULN' => $gir])->one();
            }

            
            // для определения категории земель
                    if($arritem->zk !== null) {
                        
                        // *** лесные земли ***
                        if ($arritem->zk < 200000) {
                                                        
                            // *** покрытые лесом земли ***
                            if ($arritem->zk < 105000) {
                                $zk = 'покрытые лесной растительностью'; // площадь земель, покрытых лесом
                                if ($arritem->zk > 100400){
                                    $zk = 'лесные культуры';
                                }                               
                            }
                            elseif ($arritem->zk < 106000){
                                $zk = 'несомкнувшиеся культуры';
                            }
                            elseif ($arritem->zk < 107000) {
                                $zk = 'питомники и плантации';
                            }
                            elseif ($arritem->zk == 107070) {
                                $zk = 'естественная редина';
                            }
                            elseif ($arritem->zk == 108080){
                                $zk = 'гари';
                            }
                            elseif ($arritem->zk == 109081 || $arritem->zk == 109082){
                                $zk = 'погибшие насаждения';
                            }
                            elseif ($arritem->zk >= 110084 && $arritem->zk <= 110086){
                                $zk = 'вырубки';
                            }
                            else {
                                $zk = 'прогалины, пустыри';
                            }
                        } 
                        
                        // нелесные земли
                        else {
                            if ($arritem->zk == 201100) {
                                $zk = 'пашни';
                            }
                            elseif ($arritem->zk == 202103) {
                                $zk = 'сенокосы';
                            }
                            elseif ($arritem->zk >= 203105 && $arritem->zk <= 203109) {
                                $zk = 'пастбища';
                            }
                            elseif ($arritem->zk >= 204120 && $arritem->zk <= 204130) {
                                $zk = 'воды';
                            }
                            elseif ($arritem->zk >= 205140 && $arritem->zk <= 205144) {
                                $zk = 'сады, тутовники, ягодники';
                            }
                            elseif ($arritem->zk >= 206160 && $arritem->zk <= 206174) {
                                $zk = 'дороги, просеки';
                            }
                            elseif ($arritem->zk >= 207200 && $arritem->zk <= 207234) {
                                $zk = 'усадьбы';
                            }
                            elseif ($arritem->zk >= 208260 && $arritem->zk <= 208264) {
                                $zk = 'болото';
                            }
                            elseif ($arritem->zk == 209270 && $arritem->zk == 209271){
                                $zk = 'пески';
                            }
                            elseif ($arritem->zk >= 210280 && $arritem->zk <= 210282) {
                                $zk = 'ледники';
                            }
                            else {
                                $zk = 'прочие земли';
                            }
                        } 
                    }
            
          

            $cell = array_fill(1, 10, 0); //массив д/фиксации сущ. ли у нас такой же ярус
            //цикл по ярусам
            for ($i=1; $i <= 10 ; $i++) { 
                if ($arritem->{"ard".$i} !=0 && $cell[$i] == 0) {
                    $count = 0;
                    $key = 0;
                    for ($j = ($i+1); $j <= 10; $j++) //цикл по оставшимся ярусам
                        //ус-е сущ-я ярусов с таким же зн-м 
                        if ($arritem->{"ard".$i} == $arritem->{"ard".$j}) 
                        {
                            $cell[$j] = $j;
                            $num[$key] = $j; //д/фиксации номеров таких же зн-й ярусов
                            $key++;
                            $count++; //счетчик кол-ва одинаковых по зн-ю ярусов
                        }
                    $charet = $arritem->{"kf".$i}.$arritem->{"mr".$i}; //хар-ка лесов
                    if ($count != 0)
                        //в случае сущ-я таких же зн-й ярусов, добавляем в хар-ку названия kf и mr 
                        for ($c = 0; $c <= ($count - 1); $c++) 
                            $charet .= $arritem->{"kf".$num[$c]}.$arritem->{"mr".$num[$c]};
                    if ($arritem->{"kf".$i} < 10) //сумма kf по одному ярусу должна = 10
                    {
                        $kf = $arritem->{"kf".$i}; //сумма по всем kf д/одногояруса
                        $h = $i+1;
                        //если у нас след. ярусы 0, но kf не 0
                        if (($arritem->{"kf".$h} != 0) && ($arritem->{"ard".$h} == 0)) 
                            while ($kf < 10){
                                $kf = $kf + $arritem->{"kf".$h}; 
                                $charet .= $arritem->{"kf".$h}.$arritem->{"mr".$h};
                                $key++;
                                $num[$key] = $h; //д/фиксации номеров таких же зн-й ярусов
                                $h++;
                                $count++;
                            }                        
                    }

                    //поиск редин
                    if ($arritem->{"ard".$i} == 9) $redin = $arritem->{"tur1h1".$i};
                    else $redin = 0;
    
                    
                    //полнота
                    if ($arritem->skal1 > 1) $entirety = $arritem->skal1 / 10;
                    else $entirety = $arritem->skal1;

                    $totalstock = $arritem->tur1h1*$arritem->pl; //общий запас на выделе
                    $composit = ($totalstock*$arritem->{"kf".$i}) / 10; //по состав. породам
                    //$deadwood = $arritem->sux*$arritem->pl; //запас на выделе, сухостой

                    //$person = (OivSubjectPerson::find()->where(['oiv_subject' => $numoiv, 'priority' => 1])->one())->fio;
                    $household = ''; //хозяйственные мероприятия
                    for ($d = 1; $d <= 3; $d++)
                        if ($arritem->{"up".$d} != 0) {
                            $Up = (NsiUp::find()->where(['kod' => $arritem->{"up".$d}])->one())->name;
                            if ($household != '') $household .=',&nbsp;';
                            $household .=$Up;
                        }

                    //данные доп. макета
                    $dop = " ";
                    $o = 1;
                    for ($k=1; $k <= 10 ; $k++) { 
                        if (isset($arritem->{"m".$k}) && ($arritem->{"m".$k} != 0)) {
                            $M = (NsiDm::find()->where(['kod_maket' => $arritem->{"m".$k}, 'kod' => $arritem->{"dm".$k.$o}])->one())->category_parametr;
                            $dop .= $M;
                            //$dop .= $arritem->{"m".$k};
                            $comma = 0;
                            for ($g=1; $g <= 10 ; $g++) { 
                                if (isset($arritem->{"dm".$k.$g}) && ($arritem->{"dm".$k.$g} != 0)){
                                        $Dm = (NsiDm::find()->where(['kod_maket' => $arritem->{"m".$k}, 'kod' => $arritem->{"dm".$k.$g}])->one())->name_parametr;
                                        if ($comma > 0) $dop .=",&nbsp;".$Dm;
                                        else $dop .=":&nbsp;".$Dm;
                                        $comma++;
                                }
                            }
                        }
                    }

                    echo "
                            <tr>
                                <td>".$arritem->kv."</td>
                                <td>".$arritem->sknr."</td>
                                <td>".formatter($arritem->pl)."</td>
                                <td>".$zk.'<br>'.$charet."</td>
                                <td>".$arritem->{"ard".$i}."</td>
                                <td>".$arritem->{"h".$i}."</td>
                                <td>".$arritem->{"kf".$i}."</td>
                                <td>".$arritem->{"mr".$i}."</td>
                                <td>".$arritem->{"amz".$i}."</td>
                                <td>".$arritem->{"h".$i}."</td>
                                <td>".$arritem->{"d".$i}."</td>
                                <td>".$arritem->akl."</td>
                                <td>".$arritem->agr."</td>
                                <td>".$arritem->bon."</td>
                                <td>".$arritem->mtip.$arritem->dtg."</td>
                                <td>".$entirety."</td>
                                <td>".$arritem->tur1h1."</td>
                                <td>".formatter2($totalstock)."</td>
                                <td>".formatter2($composit)."</td>
                                <td>".$arritem->{"psp".$i}."</td>
                                <td>".$arritem->sux."</td>
                                <td>".$redin."</td>
                                <td>".$arritem->svtb."</td>
                                <td>".$arritem->svtl."</td>
                                <td>".$household."</td>
                                <td>".$dop."</td>
                            </tr>
                            ";


                     if ($count != 0)
                        for ($q = 1; $q < ($count + 1); $q++) { 
                            $composit = ($totalstock*$arritem->{"kf".$num[$q]}) / 10; //по состав. породам
                            //дополнительные строки
                            echo "
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>".$arritem->{"kf".$num[$q]}."</td>
                                    <td>".$arritem->{"mr".$num[$q]}."</td>
                                    <td>".$arritem->{"amz".$num[$q]}."</td>
                                    <td>".$arritem->{"h".$num[$q]}."</td>
                                    <td>".$arritem->{"d".$num[$q]}."</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>".formatter2($composit)."</td>
                                    <td>".$arritem->{"psp".$num[$q]}."</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                ";
                        }
                


                }
            }




            // //ярусы 
            /*if ($arritem->ard1 != 0) echo "
                            <tr>
                                <td>".$arritem->sknr."</td>
                                <td>".$arritem->pl."</td>"
                                "<td>1<td>
                                <td></td>
                                <td></td>
                                <td>".$arritem->kf1."</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>";
            if ($arritem->ard2 != 0) echo "<td>2<td>";
            if ($arritem->ard3 != 0) echo "<td>3<td>";
            if ($arritem->ard4 != 0) echo "<td>4<td>";
            if ($arritem->ard5 != 0) echo "<td>5<td>";
            if ($arritem->ard6 != 0) echo "<td>6<td>";
            if ($arritem->ard7 != 0) echo "<td>7<td>";
            if ($arritem->ard8 != 0) echo "<td>8<td>";
            if ($arritem->ard9 != 0) echo "<td>9<td>";
            if ($arritem->ard10 != 0) echo "<td>10<td>";*/
           


            //echo $charet;
           // echo "            
                            //     

        }
    }

            // Если все выделы в запросе закончились, но есть флаг о произведенных рассчетах
            // выводим строку с итогами
            if($start_count) {
               
                $start_count = 0;


                // Печать итогов по лесничеству
               /* echo "
							<tr>
								<td>Итого по лесничеству </td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							";*/
            }

            ?>
    </table>




<?php
foreach ($data as $province => $level1)
{
    ?>
    <h1 class="text-center">{{$province}}</h1>
    <div class="box box-solid box-primary">
        <?php
        foreach ($level1 as $degree => $level2)
        {
            foreach ($level2 as $type => $level3)
            {
                ?>
                <div class="box-header ">{{$degree}} {{$type}}</div>
                <table class="table table-bordered table-striped"  id="dynamic_table" border="1">
                    <thead class="panel-heading">
                        <tr>
                            <th>المركز</th>
                            <th>القاضي</th>
                            <th>الإختصاص</th>
                            <th>المدور</th>
                            <th>عدد الدعاوى</th>
                            <th>الوارد</th>
                            <th>شكوى مباشرة</th>
                            <th>إدعاء نيابة</th>
                            <th>المفصول</th>
                            <th>الباقي</th>
                            <?php
                            if (isset($fields[$province][$degree][$type]))
                            {
                                foreach ($fields[$province][$degree][$type]["separated_fields"] as $field)
                                {
                                    ?>
                                    <th>{{$field}}</th>
                                    <?php
                                }
                            }
                            ?>
                            <th>المفصول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($level3 as $zone => $level4)
                        {
                            foreach ($level4 as $name => $level5)
                            {
                                ?>

                                <?php
                                foreach ($level5 as $specialities => $level6)
                                {
                                    ?>
                                    <tr>
                                        <td>{{$zone}}</td>
                                        <td>{{$name}}</td>
                                        <td>{{$specialities}}</td>
                                        <td>{{$level6["rotated"]}}</td>
                                        <td>{{$level6["total_cases"]}}</td>
                                        <td>{{$level6["arriving"]}}</td>
                                        <td>{{$level6["arrivalDirectComplaint"]}}</td>
                                        <td>{{$level6["pretencesArrival"]}}</td>
                                        <td>{{$level6["totalSeparated"]}}</td>
                                        <td>{{$level6["remainedCases"]}}</td>
                                        <?php
                                        if (isset($fields[$province][$degree][$type]))
                                        {
                                            foreach ($fields[$province][$degree][$type]["separated_fields"] as $field)
                                            {
                                                if (isset($level6["separated"][$field]))
                                                {
                                                    ?>
                                                    <td>{{$level6["separated"][$field]}}</td>
                                                    <?php
                                                } else
                                                {
                                                    ?>
                                                    <td></td>    
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        <td>{{$level6["total_separated"]}}</td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>
    </div>
    <?php
}
?>
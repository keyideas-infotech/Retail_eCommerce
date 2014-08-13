<style>
    .beacon-container {
        margin: 10px;
    }

    .filters-container {
        padding: 10px;
    }

    .filters-container .filter {
        float: left;
        padding: 10px;
    }

    .filter-error {
        color: red;
        display: block;
    }

    #bea_beacons {
        max-height: 200px;
        height: 200px;
        overflow-y: auto;
        padding: 10px;

    }
</style>
<ul id="myTab" class="nav nav-tabs">
    <li class="active"><a href="#offers" data-toggle="tab">Offers</a></li>
    <li><a href="#beacons" data-toggle="tab">Beacons</a></li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active" id="offers">
        <?php if (isset($records)): ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Offer ID</th>
                    <th>Offer Type</th>
                    <th>Offer Message</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($records) && is_array($records) && count($records)) : ?>
                    <?php foreach ($records as $record) : ?>
                        <tr>
                            <td><?php echo $record->offer_id ?></td>
                            <?php
                            $offer_type = "";
                            if ($record->offer_type == 0) {
                                $offer_type = "Walk-in";
                            } else if ($record->offer_type == 1) {
                                $offer_type = "In-store";
                            } else if ($record->offer_type == 2) {
                                $offer_type = "Eflyer";
                            }
                            ?>
                            <td><?php echo $offer_type ?></td>
                            <td><?php echo $record->offer_message ?></td>
                            <td><?php
                                if ($record->start_date != "0000-00-00 00:00:00") {
                                    $s_date = new DateTime($record->start_date);
                                    echo $s_date->format("d-m-y H:i");
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($record->end_date != "0000-00-00 00:00:00") {
                                    $e_date = new DateTime($record->end_date);
                                    echo $e_date->format("d-m-y H:i");
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No offers available...</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="tab-pane fade" id="beacons">
        <?php if (isset($beacons)): ?>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Beacon UUID</th>
                    <th>Major</th>
                    <th>Minor</th>
                    <th>Beacon Type</th>
                    <th>Beacon Label</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($beacons) && is_array($beacons) && count($beacons)) : ?>
                    <?php foreach ($beacons as $beacon) : ?>
                        <tr>
                            <td><?php echo $beacon->beacon_device_id ?></td>
                            <td><?php echo $beacon->group_id ?></td>
                            <td><?php echo $beacon->minor ?></td>
                            <?php
                            $beacon_type = "";
                            if ($beacon->beacon_type == 0) {
                                $beacon_type = "Walk-in";
                            } else if ($beacon->beacon_type == 1) {
                                $beacon_type = "In-store";
                            }
                            ?>
                            <td><?php echo $beacon_type ?></td>
                            <td><?php echo $beacon->beacon_description ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No beacons available...</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

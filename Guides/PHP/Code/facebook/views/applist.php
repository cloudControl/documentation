<table>
    <thead>
        <tr>
            <th>Application Name</th>
            <th>Application Type</th>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach ($applicationList as $application) {
        printf("<tr><td>%s</td><td>%s</td></tr>", $application->name, $application->type->name);
    }
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td>Total Apps:</td>
            <td><?php print count($applicationList); ?></td>
        </tr>
    </tfoot>
</table>
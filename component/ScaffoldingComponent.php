<?php
    $pkey = $componentData['pkey'];
    $data = $componentData['data'];

    $sampleData = end($data);
    $columns = array();
    
    $options = isset($componentData['options']) ? $componentData['options'] : array();
?>
<h1><a href="#" id="scaffoldingAddButton" class="btn btn-primary btn-mini pull-right">Add</a><?php echo $componentData['model']; ?>s</h1>
<table class="table table-bordered" id="scaffoldingTable">
	<thead>
		<tr>
			<?php 
			foreach($sampleData as $col => $val)
			{
			    $columns[] = $col;
			    echo '<th>'.(isset($options[$col]) ? $options[$col]['model'] : $col).'</th>';
			}
			?>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	<?php 
	foreach($data as $datum)
	{
	?>
		<tr id="row-<?php echo $datum['id'];?>">
			<?php 
			foreach($datum as $key => $value)
			{
			    echo '<td data-value="'.$key.'" data-editable="'.($key == $pkey ? 'no' : 'yes').'">'.$value.'</td>';
			} 
			?>
			<td><a style="display:none" href="#" class="btn btn-success btn-small" data-value='<?php echo($key['id']);?>' id='save-<?php echo($key['id']);?>'>Save</a><a href="#" class="btn btn-primary btn-small" data-value='<?php echo($key['id']);?>' id='edit-<?php echo($key['id']);?>'>Edit</a> - <a href="javascript: sendJson('<?php echo($_SERVER['REQUEST_URI']); ?>/delete',{action:'delete', data:<?php echo($key['id']); ?>})" class="btn btn-danger btn-small">Delete</a></td>
		</tr>
	<?php 
	} 
	?>
	</tbody>
</table>

<div id="addEditDialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addEditDialog" aria-hidden="false">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Add/Edit Dialog</h3>
	</div>
	<div class="modal-body">
	    <form class="form-horizontal" method="post" action="/scaffolding/update/">
	<?php
	
	// TODO: technically if the primary key is one that has a one-to-many join on it this is broken
	foreach ($sampleData as $col => $datum) 
	{
	    echo '<div class="control-group"><label class="control-label" for="'.ScaffoldingController::DATA_PREFIX.$col.'">'.(isset($options[$col]) ? $options[$col]['model'] : $col).'</label>';
	    echo '<div class="controls">';
	    if (isset($options[$col])) 
	    {
	        echo '<select id="'.ScaffoldingController::DATA_PREFIX.$col.'" name="'.ScaffoldingController::DATA_PREFIX.$col.'">';
	        echo '<option value="">[None]</option>';
	        foreach ($options[$col] as $id => $name) {
	            echo '<option value="'.$id.'">'.$name.'</option>';
	        }
	        echo '</select>';
	    }
	    else
	    {
	        if ($col == $pkey)
	        {
	            echo '<input type="text" name="'.ScaffoldingController::DATA_PREFIX.$col.'" id="'.ScaffoldingController::DATA_PREFIX.$col.'" value="'.$datum.'" readonly="readonly">';
	        }
	        else
	        {
	            echo '<input type="text" name="'.ScaffoldingController::DATA_PREFIX.$col.'" id="'.ScaffoldingController::DATA_PREFIX.$col.'">';
	        }
	    }
	    echo '</div></div>';
	}
	
	?>
	        <input type="hidden" value="create" name="action" id="action">
	        <input type="hidden" value="<?php echo $componentData['model'] ?>" name="model" id="model">
	    </form>
	</div>
	<div class="modal-footer form-actions">
		<button class="btn" data-dismiss="modal">Close</button>
		<button class="btn btn-primary" id="submit">Save changes</button>
	</div>
</div>

<script type="text/javascript"><?php

    echo 'var model = '.json_encode($componentData['model']).';';
    echo 'var pkey = '.json_encode($pkey).';';
    echo 'var scaffoldingTableColumns = '.json_encode($columns).';';
    echo 'var scaffoldingTableData = '.json_encode($data).';';
    echo 'var scaffoldingTableOptions = '.(isset($componentData['options']) ? json_encode($componentData['options']) : '{}').';';

?></script>

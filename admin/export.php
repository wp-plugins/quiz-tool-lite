<h1>Export / Import Quiz Questions</h1>
<h2>Export Questions</h2>
This page will export the quiz questions to a CSV file.

<br/><br/>
<a href="admin.php?page=ai-quiz-export&download=csv" class="button-primary">Export Questions as CSV</a><br/>


<hr/>
<h2>Import Questions</h2>

<?php
AI_Quiz_importExport::checkCSVUpload(); // Check to see if a CSV file has been uploaded
?>

<form name="csvUploadForm" method="post" action="admin.php?page=ai-quiz-export" enctype="multipart/form-data">
Upload your CSV import file.<br/>
<input type="file" name="csvFile" size="20"/>
<input type="hidden" name="mode" value="submit"><br/>
<input type="submit" value="Import Questions" class="button-primary">
</form>

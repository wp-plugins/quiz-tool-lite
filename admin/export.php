<h1>Export / Import Quiz Questions</h1>
<h2>Export</h2>
This page will export the quiz questions to the selected format.

<br/>
<a href="admin.php?page=ai-quiz-export&download=csv">Download as CSV</a><br/>

<h2>Import Questions</h2>

<?php
AI_Quiz_importExport::checkCSVUpload(); // Check to see if a CSV file has been uploaded
?>

<form name="csvUploadForm" method="post" action="admin.php?page=ai-quiz-export" enctype="multipart/form-data">
Upload your CSV import file.<br/>
<input type="file" name="csvFile" size="20"/>
<input type="hidden" name="mode" value="submit">
<input type="submit" value="Upload">
</form>

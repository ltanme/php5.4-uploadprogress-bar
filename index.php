<?php
/**
 * @category       PHP5.4 Progress Bar
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012, Pierre-Henry Soria. All Rights Reserved.
 * @license        CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @version        1.0.0
 */

/**
 * Check the version of PHP
 */
if (version_compare(phpversion(), '5.4.0', '<'))
    exit('ERROR: Your PHP version is ' . phpversion() . ' but this script requires PHP 5.4.0 or higher.');

/**
 * Check if "session upload progress" is enabled
 */
if (!intval(ini_get('session.upload_progress.enabled')))
    exit('session.upload_progress.enabled is not enabled, please activate it in your PHP config file to use this script.');

require_once 'Upload.class.php';
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
      <meta charset="utf-8" />
      <title>PHP 5.4 Session Upload Progress Bar Demo</title>
      <meta name="description" content="PHP 5.4 Session Upload Progress Bar" />
      <meta name="keywords" content="PHP, session, upload, progress bar" />
      <meta name="author" content="Pierre-Henry Soria" />
      <link rel="stylesheet" href="./static/css/common.css" />
  </head>

  <div id="container">

  <header>
    <h1>aaaa</h1>
  </header>

 <!-- <form action="upload.php?show_transfer=on" method="post" id="upload_form" enctype="multipart/form-data" target="result_frame">-->
  <form action="upload.php" method="post" id="upload_form" enctype="multipart/form-data" target="result_frame"> 
      <fieldset>
          <legend>Upload a</legend>
          <input type="hidden" name="<?php echo ini_get('session.upload_progress.name');?>" value="<?php Upload::UPLOAD_PROGRESS_PREFIX ?>" />
          <label for="file">s: <input type="file" name="files" id="file"  accept="*" required="required" />
          <small><em>You can select multiple files at once by clicking multiple files while holding down the "CTRL" key.</em></small></label>
          <label id="box"></label>
          <button type="submit" id="upload">上传!</button>
          <button type="reset" id="cancel">取消上传</button>

      <!-- Progress bar here -->
      <div id="upload_progress" class="hidden center progress">
          <div class="bar"></div>
      </div>

      </fieldset>
  </form>

  <iframe id="result_frame" name="result_frame" src="about:blank"></iframe>

  <footer>
    <p>By <strong><a href="http://ph-7.github.com">pH7</a></strong> &copy; 2012.</p>
  </footer>

</div>
  <script src="./jquery.min.js"></script>
  <script src="./static/js/ProgressBar.class.js"></script>
  <script src="./static/js/SparkMD5/spark-md5.min.js"></script>
  <script>
  $(function(){
	  $('#upload').click(function() {
		    (new UploadBar).upload();
		  });
		  $('#cancel').click(function() {
		    (new UploadBar).cancel();
		  });

		//注意此方法引用了SparkMD5库 library:https://github.com/satazor/SparkMD5
		//监听文本框变化
		document.getElementById("file").addEventListener("change", function() {
		    //声明必要的变量
		    var fileReader = new FileReader(), box = document.getElementById('box');
		    //文件分割方法（注意兼容性）
		    blobSlice = File.prototype.mozSlice || File.prototype.webkitSlice || File.prototype.slice, 
		    file = document.getElementById("file").files[0], 

		    //文件每块分割2M，计算分割详情
		    chunkSize = 2097152,                
		    chunks = Math.ceil(file.size / chunkSize), 
		    currentChunk = 0, 

		    //创建md5对象（基于SparkMD5）
		    spark = new SparkMD5();

		    //每块文件读取完毕之后的处理
		    fileReader.onload = function(e) {
		        console.log("读取文件", currentChunk + 1, "/", chunks);
		        box.innerText = "正在分析文件信息"+currentChunk + 1;
		        //每块交由sparkMD5进行计算
		        spark.appendBinary(e.target.result);
		        currentChunk++;

		        //如果文件处理完成计算MD5，如果还有分片继续处理
		        if (currentChunk < chunks) {
		            loadNext();
		        } else {
		            console.log("finished loading");
		            box.innerText = 'MD5 hash:' + spark.end()+" 文件名："+file.name+" 文件大小:"+file.size;
		            console.info("计算的Hash", spark.end());
		        }
		    };

		     //处理单片文件的上传
		     function loadNext() {
		         var start = currentChunk * chunkSize, end = start + chunkSize >= file.size ? file.size : start + chunkSize;

		         fileReader.readAsBinaryString(blobSlice.call(file, start, end));
		     }

		      loadNext();
		});
  });

  </script>
</html>

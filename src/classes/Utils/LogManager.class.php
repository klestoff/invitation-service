<?php
	/**
	 * @author Alexander A. Klestov <alan@klestoff.ru>
	 * @copyright Copyright (c) 2010, Alexander A. Klestov
	 */
	final class LogManager
	{
		private static $logFile = null;

		private static $storeValues	= array();

		public static function isWritable()
		{
			return
				self::$logFile
				&& is_file(self::$logFile)
				&& is_writable(self::$logFile);
		}

		public static function setLogFile($logFile)
		{
			self::$logFile = $logFile;

			if (!file_exists(self::$logFile))
				touch(self::$logFile);

			if (!self::isWritable())
				self::$logFile = null;
		}

		public static function storeException(Exception $e)
		{
			self::$storeValues[time()] =
				'class: '.get_class($e)."\n"
				.'code: '.$e->getCode()."\n"
				.'message: '.$e->getMessage()."\n\n"
				.$e->getTraceAsString();
		}

		public static function store($value)
		{
			self::$storeValues[time()] = $value;
		}

		public static function deliverToLog()
		{
			if (!self::isWritable())
				return;

			$file = fopen(self::$logFile, 'a+');

			foreach (self::$storeValues as $timestamp => $logMessage) {
				fwrite($file, date('Y-m-d H:i:s', $timestamp)."\n");
				fwrite($file, $logMessage."\n\n");
			}

			fclose($file);
		}

		public static function deliverToOut()
		{
			if (self::$storeValues)
				echo
					'<pre>'
					.implode("</pre><pre>", self::$storeValues)
					.'</pre>';
		}
	}
?>

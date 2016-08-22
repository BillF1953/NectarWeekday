<?php
//<?

/**
 * This script must be tied to the "Taken" report.
 * There is a corresponding set of lines to put in the Initialization tab, same as ReQueue's, see that script.
 * This is almost identical to the ReQueue's Process script, except the decision to keep a row is inversed
 * Also note that the columns of this report must be Sorted as follows:
 *   1. Incident ID   - Ascending
 *   2. Date Created  - Ascending
 *   3. Agent ID      - Descending
 * The following line of code needs to be placed in the Finish tab:
 RightNow\Connect\v1_3\ConnectAPI::commit();
 **/
global $debug_file;
global $write_mode;
global $BillingActivityTableName;
$scheduled_acct_id = 383;// This is the AccountID running the scheduled reports (currently Tyler's account but we will rename to RNT_DO_NOT_DELETE or similar)

$debug_mode = TRUE;
if ($debug_mode)
{
	if (!isset($debug_file))
		$debug_file = fopen("/vhosts/providecommerce/euf/assets/temp/logTakenProcess.txt", 'w');
}

if ($debug_mode){ fwrite($debug_file, "---Row being processed---\r\n"); fflush($debug_file); }


//Now insert into BillingActivity as necessary
if (!defined('DOCROOT')) {
	$docroot = get_cfg_var('doc_root');
	define('DOCROOT', $docroot);
}
require_once (DOCROOT . '/include/ConnectPHP/Connect_init.phph');
initConnectAPI();
try {

	// 1. see if the config to write data is turned on: CUSTOM_CFG_AGENT_STATS_SCHEDULED_RPT_WRITES
	// 2. and whether this is a scheduled run
	if (!isset($write_mode))
	{
		$write_mode = TRUE;
		$roql_result = RightNow\Connect\v1_3\ROQL::query("SELECT curAdminUser() as ID")->next();
		$row = $roql_result->next();
		if ($row)
		{
			if ($debug_mode){ fwrite($debug_file, "Current account ID is: ".$row['ID'].".\r\n"); fflush($debug_file); }
			if ($row['ID'] == $scheduled_acct_id)
			{
				$write_mode = RightNow\Connect\v1_3\Configuration::fetch('CUSTOM_CFG_AGENT_STATS_SCHEDULED_RPT_WRITES')->Value;
			}
		}


		// also set up which tables we're writing to
		$dev_mode = RightNow\Connect\v1_3\Configuration::fetch('CUSTOM_CFG_AGENT_STATS_DEV_MODE')->Value;
		if ($dev_mode)
			$BillingActivityTableName = 'RightNow\\Connect\\v1_3\\AgentStats\\BillingActivityDEV';
			else
				$BillingActivityTableName = 'RightNow\\Connect\\v1_3\\AgentStats\\BillingActivity';

				if ($debug_mode){ fwrite($debug_file, "write_mode is $write_mode and table name is $BillingActivityTableName\r\n"); fflush($debug_file); }


	}

	if ($write_mode)
	{
		// 1. see if this record already exists, unique key: (AccountID, StatTime)
		//  if so, update as necessary, return
		$billing_activity = $BillingActivityTableName::first(
				"AccountID = ".$rows[0][2]->val." AND StatTime = '".gmdate("Y-m-d\\TH:i:s\\Z", $rows[0][0]->val)."' AND IncidentID = ".$rows[0][1]->val
				);
		if ($debug_mode){ fwrite($debug_file, "Looking for row with AccountID = ".$rows[0][2]->val." AND StatTime = '".$rows[0][0]->val."' AND IncidentID = ".$rows[0][1]->val."\r\n"); fflush($debug_file); }
		if (isset($billing_activity))
		{
			if ($debug_mode){ fwrite($debug_file, "  Found, and IncID is ".$rows[0][1]->val."\r\n"); fflush($debug_file); }
			//              if ($debug_mode){ fwrite($debug_file, "    Taken is {$billing_activity->Taken} - set to 1\r\n"); fflush($debug_file); }
			//              if ($debug_mode){ fwrite($debug_file, "    IncID is ".$billing_activity->IncidentID->ID." - set to {$rows[0][1]->val}\r\n"); fflush($debug_file); }
			$change_occurred = FALSE;
			if ($billing_activity->Taken != 1)
			{
				$billing_activity->Taken = 1;
				$change_occurred = TRUE;
			}
			if ($billing_activity->IncidentID->ID != $rows[0][1]->val)
			{
				$billing_activity->IncidentID = RightNow\Connect\v1_3\Incident::fetch($rows[0][1]->val);
				$change_occurred = TRUE;
			}
			if ($change_occurred)
			{
				$billing_activity->save();
				if ($debug_mode){ fwrite($debug_file, "      Changes found, update being made\r\n"); fflush($debug_file); }
			}
			else
			{
				if ($debug_mode){ fwrite($debug_file, "      No changes found, no update being made\r\n"); fflush($debug_file); }
			}
			return $rows;
		}
	}
}
catch (Exception $err)
{
	if ($debug_mode){ fwrite($debug_file, "Error: ".$err->getMessage()."\r\n"); fflush($debug_file); }
}
// 2. else, insert the new record
try
{
	if ($write_mode)
	{
		if ($debug_mode){ fwrite($debug_file, "Inserting new rec, IncID is ".$rows[0][1]->val.", Agent is ".$rows[0][2]->val.".\r\n"); fflush($debug_file); }
		$new_billing_activity = new $BillingActivityTableName();
		$new_billing_activity->StatTime = $rows[0][0]->val;
		$new_billing_activity->AccountID = RightNow\Connect\v1_3\Account::fetch($rows[0][2]->val);
		$new_billing_activity->Taken = 1;
		$new_billing_activity->IncidentID = RightNow\Connect\v1_3\Incident::fetch($rows[0][1]->val);
		$new_billing_activity->save();
	}
}
catch (Exception $err)
{
	if ($debug_mode){ fwrite($debug_file, "Error: ".$err->getMessage()."\r\n"); fflush($debug_file); }
}


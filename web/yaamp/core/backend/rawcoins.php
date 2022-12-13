<?php
/**
 * This function adds the new markets
 * It also create new coins in the database (if present on the most common exchanges)
 */
function updateRawcoins()
{
	debuglog(__FUNCTION__);
	// exit();
	exchange_set_default('alcurex', 'disabled', true);
	exchange_set_default('altmarkets', 'disabled', true);
	exchange_set_default('binance', 'disabled', true);
	exchange_set_default('bittrex', 'disabled', true);
	exchange_set_default('bleutrade', 'disabled', true);
	exchange_set_default('bter', 'disabled', true);
	exchange_set_default('empoex', 'disabled', true);
	exchange_set_default('exbitron', 'disabled', false);
	exchange_set_default('hitbtc', 'disabled', true);
	exchange_set_default('kraken', 'disabled', true);
	exchange_set_default('kucoin', 'disabled', true);
	exchange_set_default('livecoin', 'disabled', true);
	exchange_set_default('poloniex', 'disabled', true);
	exchange_set_default('yobit', 'disabled', false);

	settings_prefetch_all();

	// $markets_name = array('zyrex','p2pb2b','bitmesh','btc-alpha','tradeogre','bibox','bittrex','bitz','bleutrade','coinbene','crex24','poloniex','c-cex','yobit','coinexchange','coinsmarkets','cryptopia','cryptobridge','cryptrade','escodex','hitbtc','kraken','alcurex','binance','gateio','nova','stex','empoex','kucoin','livecoin','shapeshift','tradesatoshi','moondex');
	$exchanges = getdbolist('db_balances');
	foreach ($exchanges as $exchange) {
		updateRawCoinExchange($exchange->name);
	}
	// $markets_name = array('crex24','binance','stex','southxchange','bittrex');
	// foreach ($markets_name as $market_name) {
	// 	updateRawCoinExchange($market_name);
	// }

	//////////////////////////////////////////////////////////

	$markets = dbocolumn("SELECT DISTINCT name FROM markets");
	foreach ($markets as $exchange) {
		if (exchange_get($exchange, 'disabled')) {
			$res = dborun("UPDATE markets SET disabled=8 WHERE name='$exchange'");
			if(!$res) continue;
			$coins = getdbolist('db_coins', "id IN (SELECT coinid FROM markets WHERE name='$exchange')");
			foreach($coins as $coin) {
				// allow to track a single market on a disabled exchange (dev test)
				if (market_get($exchange, $coin->getOfficialSymbol(), 'disabled', 1) == 0) {
					$res -= dborun("UPDATE markets SET disabled=0 WHERE name='$exchange' AND coinid={$coin->id}");
				}
			}
			debuglog("$exchange: $res markets disabled from db settings");
		} else {
			$res = dborun("UPDATE markets SET disabled=0 WHERE name='$exchange' AND disabled=8");
			if($res) debuglog("$exchange: $res markets re-enabled from db settings");
		}
	}

	dborun("DELETE FROM markets WHERE deleted");

	$list = getdbolist('db_coins', "not enable and not installed and id not in (select distinct coinid from markets)");
	foreach($list as $coin)
	{
		if ($coin->visible)
			debuglog("{$coin->symbol} is no longer active");
	// todo: proper cleanup in all tables (like "yiimp coin SYM delete")
	//	if ($coin->symbol != 'BTC')
	//		$coin->delete();
	}
}

function updateRawCoinExchange($marketname)
{
	debuglog(__FUNCTION__);
	debuglog("==== Exchange $marketname ====");
	switch ($marketname) {
		case 'altmarkets':
			if (!exchange_get($marketname, 'disabled')) 
			{
				$list = altmarkets_api_query('markets');
				if(is_array($list) && !empty($list))
				{
					// debuglog(json_encode($list));
					dborun("UPDATE markets SET deleted=true WHERE name='$marketname'");
					foreach($list as $key=>$data) {
						// debuglog(json_encode($data));
						$e = explode("/",$data->name);
						// debuglog(json_encode($e));
						$base = $e[1];
						if (strtoupper($base) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						// debuglog($symbol);
						updateRawCoin($marketname, $symbol);
					}
				}
			}
		break;

		case 'exbitron':
			if (!exchange_get($marketname, 'disabled')) 
			{
				$list = exbitron_api_query('markets');
				if(is_array($list) && !empty($list))
				{
					// debuglog(json_encode($list));
					dborun("UPDATE markets SET deleted=true WHERE name='$marketname'");
					foreach($list as $key=>$data) {
						// debuglog(json_encode($data));
						$e = explode("/",$data->name);
						// debuglog(json_encode($e));
						$base = $e[1];
						if (strtoupper($base) !== 'BTC'||strtoupper($base) !== 'USDT')
							continue;
						$symbol = strtoupper($e[0]);
						// debuglog($symbol);
						updateRawCoin($marketname, $symbol);
					}
				}
			}
		break;

		case 'occe':
			if (!exchange_get($marketname, 'disabled')) 
			{
				$list = occe_api_query('info');
				// debuglog(json_encode($list->data));
				if(is_object($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='$marketname'");
					foreach($list->data->coinInfo as $name=>$data) {
						// debuglog(json_encode($data));
						$e = explode("_",$data->pair);
						$base = $e[1];
						if (strtoupper($base) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						// debuglog($symbol);
						updateRawCoin($marketname, $symbol);
					}
				}
			}
		break;
		case 'coinex':
			if (!exchange_get($marketname, 'disabled')) 
			{
				// debuglog("Ok");
				$list = coinex_api_query('market/list');
				// debuglog(json_encode($list));
				if(is_object($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='$marketname'");
					foreach($list->data as $name=>$data) {
						// debuglog(json_encode($data));
						$base = substr($data,-3);
						if (strtoupper($base) !== 'BTC')
							continue;
						$symbol = strtoupper(substr($data,0,strlen($data)-3));
						// debuglog($symbol);
						updateRawCoin($marketname, $symbol);
					}
				}
			}
		break;
		case 'southxchange':
			if (!exchange_get('southxchange', 'disabled')) 
			{
				// debuglog("Ok");
				$list = southxchange_api_query('prices');
				// debuglog(json_encode($list));
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='southxchange'");
					foreach($list as $name=>$data) {
						// debuglog(json_encode($data));
						$e = explode("/",$data->Market);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('southxchange', $symbol);
					}
				}
			}
		break;
		case 'zyrex':
			// debuglog("Start ZYREX");
			if (!exchange_get('zyrex', 'disabled')) 
			{
				// debuglog("Ok");
				$list = zyrex_api_query('tickers.json');
				// debuglog(json_encode($list));
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='zyrex'");
					foreach($list as $name=>$data) {
						// debuglog(json_encode($data));
						$e[1] = substr($name,-3);
						$e[0] = substr($name,0,strlen($name)-3);
						debuglog("$e[1] : $e[0]");
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('zyrex', $symbol);
					}
				}
			}
		break;
		case 'p2pb2b':
			debuglog("Start P2PB2B");
			if (!exchange_get('p2pb2b', 'disabled')) 
			{
				debuglog("Ok");
				$list = p2pb2b_api_query('tickers');
				#debuglog(json_encode($list));
				if(is_object($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='p2pb2b'");
					foreach($list->result as $name=>$ticker) {
						#debuglog("==== " .$name. " ====");
						#debuglog(json_encode($ticker));
						$e = explode('_', $name);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('p2pb2b', $symbol);
					}
				}
			}
		break;
		case 'bitmesh':
			if (!exchange_get('bitmesh', 'disabled')) {
				$list = bitmesh_api_query('market.ticker');
				// debuglog(json_encode($list));
				if(is_object($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='bitmesh'");
					foreach($list->data as $ticker) {
						// debuglog(json_encode($ticker));
						$e = explode('_', $ticker->name);
						if (strtoupper($e[0]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[1]);
						updateRawCoin('bitmesh', $symbol);
					}
				}
			}
		break;
		case 'btc-alpha':
			if (!exchange_get('btc-alpha', 'disabled')) {
				$list = btcalpha_api_query('ticker');
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='btcalpha'");
					foreach($list as $ticker) {
						$e = explode('_', $ticker->pair);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('btc-alpha', $symbol);
					}
				}
			}
		break;
		case 'tradeogre':
			if (!exchange_get('tradeogre', 'disabled')) {
				$list = tradeogre_api_query('markets');
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='tradeogre'");
					foreach($list as $ticker) {
						$symbol_index = key($ticker);
						$e = explode('-', $symbol_index);
						if (strtoupper($e[0]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[1]);
						updateRawCoin('tradeogre', $symbol);
					}
				}
			}
		break;
		case 'bittrex':
			if (!exchange_get('bittrex', 'disabled')) {
				$list = bittrex_api_query('public/getcurrencies');
				if(isset($list->result) && !empty($list->result))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='bittrex'");
					foreach($list->result as $currency) {
						if ($currency->Currency == 'BTC') {
							exchange_set('bittrex', 'withdraw_fee_btc', $currency->TxFee);
							continue;
						}
						updateRawCoin('bittrex', $currency->Currency, $currency->CurrencyLong);
					}
				}
			}
		break;
		case 'txbit':
			if (!exchange_get('txbit', 'disabled')) {
				$list = txbit_api_query('public/getcurrencies');
				if(isset($list->result) && !empty($list->result))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='txbit'");
					foreach($list->result as $currency) {
						if ($currency->Currency == 'BTC') {
							exchange_set('txbit', 'withdraw_fee_btc', $currency->TxFee);
							continue;
						}
						updateRawCoin('txbit', $currency->Currency, $currency->CurrencyLong);
					}
				}
			}
		break;
		case 'bitz':
			if (!exchange_get('bitz', 'disabled')) {
				$list = bitz_api_query('tickerall');
				if (!empty($list)) {
					dborun("UPDATE markets SET deleted=true WHERE name='bitz'");
					foreach($list as $c => $ticker) {
						$e = explode('_', $c);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('bitz', $symbol);
					}
				}
			}
		break;
		case 'bleutrade':
			if (!exchange_get('bleutrade', 'disabled')) {
				$list = bleutrade_api_query('public/getcurrencies');
				if(isset($list->result) && !empty($list->result))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='bleutrade'");
					foreach($list->result as $currency) {
						if ($currency->Currency == 'BTC') {
							exchange_set('bleutrade', 'withdraw_fee_btc', $currency->TxFee);
							continue;
						}
						updateRawCoin('bleutrade', $currency->Currency, $currency->CurrencyLong);
					}
				}
			}
		break;
		case 'coinbene':
			if (!exchange_get('coinbene', 'disabled')) {
				$data = coinbene_api_query('market/symbol');
				$list = objSafeVal($data, 'symbol');
				if(is_array($list) && !empty($list)) {
					dborun("UPDATE markets SET deleted=true WHERE name='coinbene'");
					foreach($list as $ticker) {
						if ($ticker->quoteAsset != 'BTC') continue;
						$symbol = $ticker->baseAsset;
						updateRawCoin('coinbene', $symbol);
					}
				}
			}
		break;
		case 'crex24':
			if (!exchange_get('crex24', 'disabled')) {
				$list = crex24_api_query('currencies');
				if(is_array($list) && !empty($list)) {
					dborun("UPDATE markets SET deleted=true WHERE name='crex24'");
					foreach ($list as $currency) {
						$symbol = objSafeVal($currency, 'symbol');
						$name = objSafeVal($currency, 'name');
						if ($currency->isFiat || $currency->isDelisted) continue;
						updateRawCoin('crex24', $symbol, $name);
					}
				}
			}
		break;
		case 'poloniex':
			if (!exchange_get('poloniex', 'disabled')) {
				$poloniex = new poloniex;
				$tickers = $poloniex->get_currencies();
				if (!$tickers)
					$tickers = array();
				else
					dborun("UPDATE markets SET deleted=true WHERE name='poloniex'");
				foreach($tickers as $symbol=>$ticker)
				{
					if(arraySafeVal($ticker,'disabled')) continue;
					if(arraySafeVal($ticker,'delisted')) continue;
					updateRawCoin('poloniex', $symbol);
				}
			}
		break;
		case 'yobit':
			if (!exchange_get('yobit', 'disabled')) {
				$res = yobit_api_query('info');
				if($res)
				{
					dborun("UPDATE markets SET deleted=true WHERE name='yobit'");
					foreach($res->pairs as $i=>$item)
					{
						$e = explode('_', $i);
						$symbol = strtoupper($e[0]);
						updateRawCoin('yobit', $symbol);
					}
				}
			}
		break;
		case 'coinexchange':
			if (!exchange_get('coinexchange', 'disabled')) {
				$list = coinexchange_api_query('getmarkets');
				if(isset($list->result) && !empty($list->result))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='coinexchange'");
					foreach($list->result as $item) {
						if ($item->BaseCurrencyCode != 'BTC')
							continue;
						$symbol = $item->MarketAssetCode;
						$label = objSafeVal($item, 'MarketAssetName');
						updateRawCoin('coinexchange', $symbol, $label);
					}
				}
			}
		break;
		case 'coinsmarkets':
			if (!exchange_get('coinsmarkets', 'disabled')) {
				$list = coinsmarkets_api_query('apicoin');
				if(!empty($list) && is_array($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='coinsmarkets'");
					foreach($list as $pair=>$data) {
						$e = explode('_', $pair);
						if ($e[0] != 'BTC') continue;
						$symbol = strtoupper($e[1]);
						updateRawCoin('coinsmarkets', $symbol);
					}
				}
			}
		break;
		case 'cryptopia':
			if (!exchange_get('cryptopia', 'disabled')) {
				$list = cryptopia_api_query('GetMarkets');
				if(isset($list->Data))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='cryptopia'");
					foreach($list->Data as $item) {
						$e = explode('/', $item->Label);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('cryptopia', $symbol);
					}
				}
			}
		break;
		case 'cryptobridge':
			if (!exchange_get('cryptobridge', 'disabled')) {
				$list = cryptobridge_api_query('ticker');
				if(is_array($list) && !empty($list))
				{
					#dborun("UPDATE markets SET deleted=true WHERE name='cryptobridge'");
					foreach($list as $ticker) {
						$e = explode('_', $ticker->id);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('cryptobridge', $symbol);
					}
				}
			}
		break;
		case 'cryptrade':
			if (!exchange_get('cryptrade', 'disabled')) {
				$list = cryptrade_api_query('tickers');
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='cryptrade'");
					foreach($list as $ticker) {
						$e = explode('_', $ticker->id);
						if (strtoupper($e[1]) !== 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('cryptrade', $symbol);
					}
				}
			}
		break;
		case 'escodex':
			if (!exchange_get('escodex', 'disabled')) {
				$list = escodex_api_query('ticker');
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='escodex'");
					foreach($list as $ticker) {
						#debuglog (json_encode($ticker));
						if (strtoupper($ticker->base) !== 'BTC')
							continue;
						$symbol = strtoupper($ticker->quote);
						updateRawCoin('escodex', $symbol);
					}
				}
			}
		break;
		case 'hitbtc':
			if (!exchange_get('hitbtc', 'disabled')) {
				$list = hitbtc_api_query('symbols');
				if(is_object($list) && isset($list->symbols) && is_array($list->symbols))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='hitbtc'");
					foreach($list->symbols as $data) {
						$base = strtoupper($data->currency);
						if ($base != 'BTC') continue;
						$symbol = strtoupper($data->commodity);
						updateRawCoin('hitbtc', $symbol);
					}
				}
			}
		break;
		case 'kraken':
			if (!exchange_get('kraken', 'disabled')) {
				$list = kraken_api_query('AssetPairs');
				if(is_array($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='kraken'");
					foreach($list as $pair => $item) {
						$pairs = explode('-', $pair);
						$base = reset($pairs); $symbol = end($pairs);
						if($symbol == 'BTC' || $base != 'BTC') continue;
						if(in_array($symbol, array('GBP','CAD','EUR','USD','JPY'))) continue;
						if(strpos($symbol,'.d') !== false) continue;
						$symbol = strtoupper($symbol);
						updateRawCoin('kraken', $symbol);
					}
				}
			}
		break;
		case 'alcurex':
			if (!exchange_get('alcurex', 'disabled')) {
				$list = alcurex_api_query('market','?info=on');
				if(is_object($list) && isset($list->MARKETS))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='alcurex'");
					foreach($list->MARKETS as $item) {
						$e = explode('_', $item->Pair);
						$symbol = strtoupper($e[0]);
						updateRawCoin('alcurex', $symbol);
					}
				}
			}
		break;
		case 'binance':
			if (!exchange_get('binance', 'disabled')) {
				$list = binance_api_query('ticker/allBookTickers');
				if(is_array($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='binance'");
					foreach($list as $ticker) {
						$base = substr($ticker->symbol, -3, 3);
						// XXXBTC XXXETH BTCUSDT (no separator!)
						if ($base != 'BTC') continue;
						$symbol = substr($ticker->symbol, 0, strlen($ticker->symbol)-3);
						updateRawCoin('binance', $symbol);
					}
				}
			}
		break;
		case 'gateio':
			if (!exchange_get('gateio', 'disabled')) {
				$json = gateio_api_query('marketlist');
				$list = arraySafeVal($json,'data');
				if(!empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='gateio'");
					foreach($list as $item) {
						if ($item['curr_b'] != 'BTC')
							continue;
						$symbol = trim(strtoupper($item['symbol']));
						$name = trim($item['name']);
						updateRawCoin('gateio', $symbol, $name);
					}
				}
			}
		break;
		case 'nova':
			if (!exchange_get('nova', 'disabled')) {
				$list = nova_api_query('markets');
				if(is_object($list) && !empty($list->markets))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='nova'");
					foreach($list->markets as $item) {
						if ($item->basecurrency != 'BTC')
							continue;
						$symbol = strtoupper($item->currency);
						updateRawCoin('nova', $symbol);
						//debuglog("nova: $symbol");
					}
				}
			}
		break;
		case 'stex':
			// debuglog("Start STEX");
			if (!exchange_get('stex', 'disabled')) {
				$list = stex_api_query('currencies');
				if(is_object($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='stex'");
					foreach($list->data as $item) {
						if ($item->code == 'BTC') continue;
						if (!$item->active) continue;
						$symbol = strtoupper($item->code);
						$name = trim($item->name);
						updateRawCoin('stex', $symbol, $name);
					}
				}
			} else {
				debuglog ("Disable by Setting");
			}
		break;
		case 'empoex':
			if (!exchange_get('empoex', 'disabled')) {
				$list = empoex_api_query('marketinfo');
				if(is_array($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='empoex'");
					foreach($list as $item) {
						$e = explode('-', $item->pairname);
						$base = strtoupper($e[1]);
						if ($base != 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('empoex', $symbol);
					}
				}
			}
		break;
		case 'kucoin':
			if (!exchange_get('kucoin', 'disabled')) {
				$list = kucoin_api_query('currencies');
				if(kucoin_result_valid($list) && !empty($list->data))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='kucoin'");
					foreach($list->data as $item) {
						$symbol = $item->name;
						$name = $item->fullName;
						updateRawCoin('kucoin', $symbol, $name);
					}
				}
			}
		break;
		case 'livecoin':
			if (!exchange_get('livecoin', 'disabled')) {
				$list = livecoin_api_query('exchange/ticker');
				if(is_array($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='livecoin'");
					foreach($list as $item) {
						$e = explode('/', $item->symbol);
						$base = strtoupper($e[1]);
						if ($base != 'BTC')
							continue;
						$symbol = strtoupper($e[0]);
						updateRawCoin('livecoin', $symbol);
					}
				}
			}
		break;
		case 'shapeshift':
			if (!exchange_get('shapeshift', 'disabled')) {
				$list = shapeshift_api_query('getcoins');
				if(is_array($list) && !empty($list))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='shapeshift'");
					foreach($list as $item) {
						$status = $item['status'];
						if ($status != 'available') continue;
						$symbol = strtoupper($item['symbol']);
						$name = trim($item['name']);
						updateRawCoin('shapeshift', $symbol, $name);
						//debuglog("shapeshift: $symbol $name");
					}
				}
			}
		break;
		case 'tradesatoshi':
			if (!exchange_get('tradesatoshi', 'disabled')) {
				$data = tradesatoshi_api_query('getcurrencies');
				if(is_object($data) && !empty($data->result))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='tradesatoshi'");
					foreach($data->result as $item) {
						$symbol = $item->currency;
						$name = trim($item->currencyLong);
						updateRawCoin('tradesatoshi', $symbol, $name);
					}
				}
			}
		break;
		case 'moondex':
			function updateRawCoinsMoonDex()
			{
				if (!exchange_get('moondex', 'disabled')) {
					$list = moondex_api_query('getmarkets');
					if(is_object($list))
					{
						#dborun("UPDATE markets SET deleted=true WHERE name='cryptobridge'");
						foreach($list->result as $ticker) {
							$e = explode('-', $ticker->MarketName);
							if (strtoupper($e[0]) !== 'BTC')
								continue;
							$symbol = strtoupper($e[1]);
							updateRawCoin('moondex', $symbol);
						}
					}
				}
			}
		break;
		case 'bibox':
			if (!exchange_get('bibox', 'disabled')) {
				$list = bibox_api_query('marketAll');
				if(isset($list["result"]) && !empty($list["result"]))
				{
					dborun("UPDATE markets SET deleted=true WHERE name='bibox'");
					foreach($list["result"] as $currency) {
						if ($currency["currency_symbol"] == 'BTC') continue;
						updateRawCoin('bibox', $currency["coin_symbol"]);
					}
				}
			}
		break;
	}
	debuglog("==== END Exchange ====");
}

function updateRawCoin($marketname, $symbol, $name='unknown')
{
	if($symbol == 'BTC') return;

	$coin = getdbosql('db_coins', "symbol=:symbol", array(':symbol'=>$symbol));
	if(!$coin && YAAMP_CREATE_NEW_COINS)
	{
		$algo = '';
		if ($marketname == 'cryptopia') {
			// get coin label and algo (different api)
			$labels = cryptopia_api_query('GetCurrencies');
			if (is_object($labels) && !empty($labels->Data)) {
				foreach ($labels->Data as $coin) {
					if ($coin->Symbol == $symbol) {
						$name = $coin->Name;
						$algo = strtolower($coin->Algorithm);
						if ($algo == 'scrypt') $algo = ''; // cryptopia default generally wrong
						break;
					}
				}
			}
		}

		if (in_array($marketname, array('nova','askcoin','binance','bitz','coinexchange','coinsmarkets','hitbtc'))) {
			// don't polute too much the db with new coins, its better from exchanges with labels
			return;
		}

		// some other to ignore...
		if (in_array($marketname, array('yobit','coinbene','kucoin','tradesatoshi')))
			return;

		if (market_get($marketname, $symbol, "disabled")) {
			return;
		}

		debuglog("new coin $marketname $symbol $name");

		$coin = new db_coins;
		$coin->txmessage = true;
		$coin->hassubmitblock = true;
		$coin->name = $name;
		$coin->algo = $algo;
		$coin->symbol = $symbol;
		$coin->created = time();
		$coin->save();

		$url = getMarketUrl($coin, $marketname);
		// if (YAAMP_NOTIFY_NEW_COINS)
		// 	mail(YAAMP_ADMIN_EMAIL, "New coin $symbol", "new coin $symbol ($name) on $marketname\r\n\r\n$url");
		sleep(1);
	}

	else if($coin && $coin->name == 'unknown' && $name != 'unknown')
	{
		$coin->name = $name;
		$coin->save();
	}

	$list = getdbolist('db_coins', "symbol=:symbol or symbol2=:symbol", array(':symbol'=>$symbol));
	foreach($list as $coin)
	{
		$market = getdbosql('db_markets', "coinid=$coin->id and name='$marketname'");
		if(!$market)
		{
			$market = new db_markets;
			$market->coinid = $coin->id;
			$market->name = $marketname;
		}

		$market->deleted = false;
		$market->save();
	}

}


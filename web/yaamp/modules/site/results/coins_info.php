<?php

$algo = user()->getState('yaamp-algo');
//if($algo == 'all') return;

echo "<div class='main-left-box'>";
echo "<div class='main-left-title'>Coin Information ($algo)</div>";
echo "<div class='main-left-inner'>";

echo <<<END
<style type="text/css">
td.symb, th.symb {
	width: 50px;
	max-width: 50px;
	text-align: right;
}
td.symb {
	font-size: .8em;
}
</style>

<table class="dataGrid2">
<thead>
<tr>
<th></th>
<th>Name</th>
<th>Information</th>
</tr>
</thead>
END;

$main_ids = array();

$algo = user()->getState('yaamp-algo');
if ($algo == 'all')
    $list = getdbolist('db_coins', "enable and visible order by index_avg desc");
else
    $list = getdbolist('db_coins', "enable and visible and algo=:algo order by index_avg desc", array(
        ':algo' => $algo
    ));

foreach($list as $coin)
{
	$id = $coin->id;
	$name = substr($coin->name, 0, 20);
	
	echo '<tr class="ssrow">';
	echo '<td width=18><img width=16 src="'.$coin->image.'"></td>';
	echo '<td><a href="/site/block?id='.$id.'"><b>'.$name.' </b></a></td>';
	echo '<td>';
	if($coin->link_bitcointalk) echo '<a href="'.$coin->link_bitcointalk.'"><img width=16 src="/images/bitcointalk.webp">Bitcointalk Forum</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_site) echo '<a href="'.$coin->link_site.'"><img width=16 src="/images/home.webp">Website</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_discord) echo '<a href="'.$coin->link_discord.'"><img width=16 src="/images/discord.png">Discord</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_explorer) echo '<a href="'.$coin->link_explorer.'"><img width=16 src="/images/blockchain.webp">Explorer</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_github) echo '<a href="'.$coin->link_github.'"><img width=16 src="/images/Github.png">Github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_exchange) echo '<a href="'.$coin->link_exchange.'"><img width=16 src="/images/exchange.png">Exchange</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	if($coin->link_twitter) echo '<a href="'.$coin->link_twitter.'"><img width=16 src="/images/Twitter.png">Twitter</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a href="/explorer/peers?id='.$id.'"><img width=16 src="/images/nodes16.png">Nodes</a>';
	echo '</td>';
	echo '</tr>';
}

echo '</table>';


echo '</div>';

echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo '</div></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';







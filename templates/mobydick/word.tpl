{~INCLUDE templates/mobydick/header.tpl~}
<h1><em>Moby-Dick</em> Word Search</h1>
{~IF error~}
<div id="error">{~ERROR~}</div>
{~/IF~}
{~IF word~}
<h3>Results for {~WORD~}</h3>
{~/IF~}

{~IF total ==0~}
<p>No results found. Try another search!</p>
{~/IF~}

{~INCLUDE templates/mobydick/search_form.tpl~}

{~IF chapters~}
<div><img src="http://chart.apis.google.com/chart?cht=lc&chd=t:{~CHART_DATA~}&chds={~CHART_MIN~},{~CHART_MAX~}&chs=1000x200&chxt=x,y,x,x&chxl=0:|{~CHART_LABELS_EVEN~}|2:|{~CHART_LABELS_ODD~}|3:|Chapter|&chxp=3,45&chxs=0,000000,8|2,000000,8&chxr=1,0,{~CHART_MAX~}&chco=4A77D9&chtt=Instances of '{~WORD~}' in Moby-Dick by chapter" /></div>
<table>
	<tr>
		<td style="font-weight: bold;">Chapter No.</td>
		<td style="font-weight: bold;">Chapter Title</td>
		<td style="font-weight: bold;">Occurrences</td>
	</tr>
{~FOR chapters~}
	<tr>
		<td align="right" style="padding-right: 8px;">{~CHAPTER_ID~}</td>
		<td><a href="chapter.php?chapter={~CHAPTER_ID~}">{~TITLE~}</a></td>
		<td align="right">{~NUM~}</td>
	</tr>	
{~/FOR chapters~}
	<tr>
		<td align="right">&nbsp;</td>
		<td><strong>Total</strong></td>
		<td align="right">{~TOTAL~}</td>
	</tr>
</table>
{~/IF~}
{~INCLUDE templates/mobydick/footer.tpl~}

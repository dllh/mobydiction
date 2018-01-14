{~INCLUDE templates/mobydick/header.tpl~}
<h1><em>Moby-Dick</em> Word Search Omissions</h1>
{~IF error~}
<div id="error">{~ERROR~}</div>
{~/IF~}

<p>Some words probably just aren't that interesting to list per chapter. It's subjective, I suppose (someone interested in charting indefinite vs. definite articles will disagree with my choices here), but I opted to omit certain such words from the "top words" count in the sidebar for the chapter view. Without these omissions, hardly any interesting words appear in the sidebar. You can still search for these words to see their frequency throughout the book. Here's the current list of omitted words:

{~IF words~}
<ul>
{~FOR words~}
	<li><a href="word.php?word={~WORD~}">{~WORD~}</a></li>
{~/FOR words~}
</ul>
{~/IF~}
{~INCLUDE templates/mobydick/footer.tpl~}

{~INCLUDE templates/mobydick/header.tpl~}
{~IF words~}
<div id="word_sidebar">
	<h3>Top Words</h3>
	<p style="font-size: 9pt;">Click a word to see its frequency and other chapters in which it appears.</p>
	<table>
		<tr>
			<td style="font-weight:bold;">Word</td>
			<td style="font-weight: bold;">Occurrences</td>
		</tr>
	{~FOR words~}
		<tr>
			<td><a href="word.php?word={~WORD~}">{~WORD~}</a></td>
			<td>{~NUM~}</td>
		</tr>	
	{~/FOR words~}
	</table>
</div>
<div id="chapter">
	<h2>Chapter {~CHAPTER~}: {~CHAPTER_TITLE~}</h2>
	<div class="chapter_nav">
		<div id="prev_chapter"><a href="chapter.php?chapter={~PREV_CHAPTER~}">&lt;&lt; Previous Chapter</a></div>
		<div id="next_chapter"><a href="chapter.php?chapter={~NEXT_CHAPTER~}">Next Chapter &gt;&gt;</a></div>
	</div>
	<pre>
	{~CHAPTER_TEXT~}
	</pre>
	<div class="chapter_nav">
		<div id="prev_chapter"><a href="chapter.php?chapter={~PREV_CHAPTER~}">&lt;&lt; Previous Chapter</a></div>
		<div id="next_chapter"><a href="chapter.php?chapter={~NEXT_CHAPTER~}">Next Chapter &gt;&gt;</a></div>
	</div>
</div>
{~/IF~}
{~INCLUDE templates/mobydick/footer.tpl~}

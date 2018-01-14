{~INCLUDE templates/mobydick/header.tpl~}
<h1><em>Moby-Dick</em> Word Search</h1>
<p class="intro">Type in a word to see where it appears in <em>Moby-Dick</em>, and with what frequency. A graph will give you a visual representation, and clicking on one of the chapters containing the word will take you to a list of the top words for that chapter. You can then in turn click words in that list to see what chapters they appear in. Clicking back and forth in this way turns out to be very addictive!</p>

<p class="intro"><strong>Note:</strong> The search feature currently handles only one word at a time. Including spaces or operators such as "+" will not return valid results. You <em>can</em> do wildcards using the % sign, however. So to search for woman and women, you might try "wom%n".</p>

{~IF word~}
<h3>Results for {~WORD~}</h3>
{~/IF~}

{~INCLUDE templates/mobydick/search_form.tpl~}

{~INCLUDE templates/mobydick/footer.tpl~}

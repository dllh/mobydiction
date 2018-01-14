function validate(field) {
	if(field.value.match(/ /)){
		alert('Try searching for just one word. This is a word frequency search and not a full-text search.');
		field.focus();
		return false;
	}
	return true;
}

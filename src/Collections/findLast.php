<?php

namespace Dash\Collections;

function findLast($collection, $predicate)
{
	$values = mapValues($collection);
	$reversed = reverse($values);
	return find($reversed, $predicate);
}
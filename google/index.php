<?php

# Google Code Recovery

if (!empty($_GET['code'])) {
	echo '<strong>Código (use no cli): </strong>' . $_GET['code'];
	return;
}

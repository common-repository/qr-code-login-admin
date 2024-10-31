<select id="QRLA_time_limit" name="QRLA_time_limit">
	<?php for( $i = 1; $i <= 365; $i += 1 ): ?>
		<option <?php selected( $selected_time, $i ); ?> value="<?php echo esc_html( __($i ) ); ?>"><?php printf( __( '%d Days', 'automaticqrla' ), $i ); ?></option>
	<?php endfor; ?>
</select>

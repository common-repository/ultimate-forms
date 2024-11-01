<!-- Upgrade to pro link box -->
<!-- TOP BOX-->

<?php global $wpdb;
?>
<div id="fade" class="ewd-ufp-dark_overlay"></div>
<div id="ewd-dashboard-top" class="metabox-holder">

	<div id="ewd-dashboard-box-orders" class="ewd-ufp-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/ufp-buttonsicons-full-06.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value"><span class="displaying-num"><?php echo $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type='ufp_form' AND post_status='publish'"); ?></span>
		  </div>
		  <div class="ewd-dashboard-box-field">Forms
		  </div>
		</div>
	</div>
	<div id="ewd-dashboard-box-links" class="ewd-ufp-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/ufp-buttonsicons-05.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value ewd-font-22"><?php echo $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_type='ufp_form' ORDER BY post_date DESC"); ?>
		  </div>
		  <div class="ewd-dashboard-box-field">Last Form Created
		  </div>
		</div>
	</div>
	<div id="ewd-dashboard-box-views" class="ewd-ufp-dashboard-box" >
	  	<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/ufp-buttonsicons-03.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  <div class="ewd-dashboard-box-value"><?php echo $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key='ufp_form_submissions'"); ?>
		  </div>
		  <div class="ewd-dashboard-box-field">Submissions
		  </div>
		</div>
	</div>

	<div id="ewd-dashboard-box-support" class="ewd-ufp-dashboard-box" >
		<div class="ewd-dashboard-box-icon"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/ufp-buttonsicons-04.png"/>
	  	</div>
		<div class="ewd-dashboard-box-value-and-field-container">
		  	<div class="ewd-dashboard-box-support-value">
			<form id="form1" runat="server">
			<a href="javascript:void(0)" onclick="document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'">Click here for support</a>
		  		</div>
			</div>
		</div>
	<div id="light" class="ewd-ufp-bright_content">
            <asp:Label ID="lbltext" runat="server" Text="Hey there!"></asp:Label>
            <a href="javascript:void(0)" onclick="document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
		</br>
		<h2>Need help?</h2>
		<p>You may find the information you need with our support tools.</p>
		<a href="https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/support_icons_ufp-01.png" /></a>
		<a href="https://www.youtube.com/channel/UCZPuaoetCJB1vZOmpnMxJNw"><h4>Youtube Tutorials</h4></a>
		<p>Our tutorials show you the basics of setting up your plugin, to the more specific utilization of our features.</p>
		<div class="ewd-ufp-clear"></div>
		<a href="https://wordpress.org/support/plugin/ultimate-forms"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/support_icons_ufp-03.png"/></a>
		<a href="https://wordpress.org/support/plugin/ultimate-forms"><h4>WordPress Forum</h4></a>
		<p>We make sure to answer your questions within a 24hrs frame during our business days. Search within our threads to find your answers. If it has not been addressed, please create a new thread!</p>
		<div class="ewd-ufp-clear"></div>
		<a href="http://www.etoilewebdesign.com/plugins/"><img src="<?php echo plugins_url(); ?>/ultimate-forms/images/support_icons_ufp-02.png"/></a>
		<a href="http://www.etoilewebdesign.com/plugins/"><h4>Documentation</h4></a>
		<p>Most information concerning the installation, the shortcodes and the features are found within our documentation page.</p>
        </div>
	</form>

<!--END TOP BOX-->
</div>



<!--Middle box-->
<div class="ewd-dashboard-middle">
<div id="col-full">
<h3 class="ewd-ufp-dashboard-h3">Forms Summary</h3>
<div>
	<table class='ewd-ufp-overview-table wp-list-table widefat fixed striped posts'>
		<thead>
			<tr>
				<th><?php _e("Title", 'EWD_ABCO'); ?></th>
				<th><?php _e("Fields", 'EWD_ABCO'); ?></th>
				<th><?php _e("Submissions", 'EWD_ABCO'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$args = array(
					'post_type' => 'ufp_form'
				);

				$Dashboard_Forms_Query = new WP_Query($args);
				$Dashboard_Forms = $Dashboard_Forms_Query->get_posts();

				if (sizeOf($Dashboard_Forms) == 0) {echo "<tr><td colspan='3'>" . __("No forms to display yet. Create a form and then view it for it to be displayed here.", 'ultimate-forms') . "</td></tr>";}
				else {
					foreach ($Dashboard_Forms as $Dashboard_Form) { ?>
						<tr>
							<td><a href='post.php?post=<?php echo $Dashboard_Form->ID;?>&action=edit'><?php echo $Dashboard_Form->post_title; ?></a></td>
							<td><?php echo get_post_meta($Dashboard_Form->ID, 'urp_form_submissions', true); ?></td>
							<td><?php echo sizeOf(get_post_meta($Dashboard_Form->ID, 'ufp_form_fields', true)); ?></td>
						</tr>
					<?php }
				}
			?>
		</tbody>
	</table>
</div>
<br class="clear" />
</div>
</div>

<!-- END MIDDLE BOX -->


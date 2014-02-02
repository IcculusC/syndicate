jQuery(function()
{
	jQuery("#share").click(function()
	{	
		jQuery.post
		(
			post.emailScript,
			{
				title: post.title,
				content: post.content,
				email: post.mailto,
				from: post.mailfrom,
				id: post.id,
				user: post.user
			}
		).done(function()
			{
				jQuery("#share").attr('disabled','disabled');
			}
		);
	});
});
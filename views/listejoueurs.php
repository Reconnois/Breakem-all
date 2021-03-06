<section class="low-height bg-cover-listejoueurs relative">

	<div class="align full-height">
		<div class="align full-height animation fadeLeft">
			<span class="header-title admin-header-title border-full relative">Nos utilisateurs</span>
		</div>
		</div>
	</div> 
</section>
<div class="list-players">	
	<table>
		<thead>
			<th>Image</th><th>Pseudo</th><th class="desc">Description</th><th>Team</th><th>Profil</th>
		</thead>
		<tbody>
		<?php 
		if(isset($liste) && is_array($liste)){
			foreach ($liste as $key => $joueur) {
			?>
			<tr>	
				<td class="picture">
					<?php echo '<img class="img-player" src="'.$joueur->getImg().'">';?>
				</td>
				<td class="pseudo">
					<a href="<?php echo WEBPATH.'/profil?pseudo='.$joueur->getPseudo(); ?>"><?php echo $joueur->getPseudo(); ?></a>
				</td>
				<td class="desc">
					<span><?php echo $joueur->getDescription(); ?></span>
				</td>
				<td class="team">
					<?php echo ($joueur->getNameTeam()) ? "<a href='".WEBPATH."/detailteam?name=".$joueur->getNameTeam()."'>".$joueur->getNameTeam()."</a>" : "" ; ?>
				</td>
				<td class="page">
					<span class="btn btn-pink">
						<a href="<?php echo WEBPATH.'/profil?pseudo='.$joueur->getPseudo(); ?>">Accèder à la page du joueur</a>
					</span>
				</td>																			
			</tr>																							
			<?php
			}
		}
		?>
		</tbody>
	</table>
</div>
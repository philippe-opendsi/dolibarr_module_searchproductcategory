<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) 2015 ATM Consulting <support@atm-consulting.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file    class/actions_searchproductcategory.class.php
 * \ingroup searchproductcategory
 * \brief   This file is an example hook overload class file
 *          Put some comments here
 */

/**
 * Class ActionsSearchProductCategory
 */
class ActionsSearchProductCategory
{
	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var array Errors
	 */
	public $errors = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array()         $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    &$object        The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          &$action        Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             < 0 on error, 0 on success, 1 to replace standard code
	 */
	function formAddObjectLine ($parameters, &$object, &$action, $hookmanager) {
		
		global $db,$langs,$user,$conf;

		$TContext = explode(':',$parameters['context']);

		if (in_array('propalcard',$TContext) || in_array('ordercard',$TContext) || in_array('invoicecard',$TContext)) 
        {
        	
			if($user->rights->searchproductcategory->user->search) {
        	//Charger les liste des projets de type feuille de temps pas encore facturé
	
	        	$langs->load('searchproductcategory@searchproductcategory');

	        	?><script type="text/javascript">
	        		var spc_object_type = '<?php echo $object->element ?>';
	        		var spc_object_id = '<?php echo $object->id ?>';
	        	</script>
				<tr class="liste_titre nodrag nodrop">
					<td colspan="9"><?php echo $langs->trans('SearchByCategory') ?></td>
					<td></td>
				</tr>
				<tr class="pair">
					<td colspan="7">
						<div id="arboresenceCategoryProduct" spc-role="arbo-multiple">
							
						</div>
					</td>
					<td valign="middle" align="center">
						<input id="qty_spc" type="text" value="1" size="5" class="flat" />
						<input id="addline_spc" class="button" type="button" name="addline_timesheet" value="<?php echo $langs->trans('Add') ?>">
					</td>
				</tr>
				<?php				
			}

        }

		return 0;
	}
}
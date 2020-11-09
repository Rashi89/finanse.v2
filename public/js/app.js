$.validator.addMethod('validPassword',
		function(value,element,param){
				if(value !=''){
							//czy conajmniej jedna litera
							if(value.match(/.*[a-z]+.*/i) == null){
									return false;
							}
							//czy conajmniej jedna cyfra
							if(value.match(/.*\d+.*/) == null){
									return false;
							}
						}
					return true;
				},
				'Must contain at least one letter and one number'
		
		);
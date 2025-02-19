
function include(arr, obj) {
    for(var i=0; i<arr.length; i++) {
        if (arr[i] == obj) return true;
    }
}

function checked(name) {
    var a = [];

    $("input[name='" + name + "']:checked").each(function() {
        a.push($(this).val());
    });
    return a;
}

function read_num(name) {
    return parseInt($("input[name='" + name + "']").val());
}

function read_atoms(name) {
    var a = $("input[name='" + name + "']").val().split(/ *, */);
    return $.grep(a, function (n) {  return n; });
}

function form_value() {
    return {
		code: $("input[name='code']").val(),
        num_exercises: read_num('num_exercises') * num_students,
        num_students: num_students,
        atoms: read_atoms('atoms'),
        compl_min: read_num('compl_min'),
        compl_max: read_num('compl_max'),
        num_premises: read_num('num_premises'),
        conectives: checked('conectives'),
        restrictions: checked('restrictions'),
        course: $("input[name='course']").val()
    };
}

function proofweb(e) {
    return e.replace(/&/g, "/\\").replace(/\|/g, "\\/").replace(/!/g, "~");
}

function natural_ded(atoms, ex, n) {

    var m = ["Require Import ProofWeb.",
	     "(* Try to Prove it with Natural Deduction: *)",
	     "Parameter "].join('\n\n');

    m += atoms.join(' ');

    m += ": Prop.\n\n";

    var k = 0;

    for(i in ex.premises) {
	m += "Hypothesis P" + k + " : ";
	m += proofweb(ex.premises[i]) + ".\n\n";
	k = k + 1;
    }

    m += "Theorem T" + n + " : " + proofweb(ex.conclusion) + ".\n\n";

    m += ["Proof.",
	  "(* prop_proof *)",
	  "Qed.\n"].join("\n\n");

    return m;
}

function semantic(atoms, ex, n) {

    var m = ["Require Import Semantics.",
	    "(* Try to Refute it with Semantics: *)",
	     "Parameter "].join('\n\n');

    m += atoms.join(' ');

    m += ": Prop.\n\n";
    m += "(* Edit ||-- or ||-/- on Hypothesis.*)\n\n";

    var k = 0;

    for(i in atoms) {
	m += "(* Hypothesis P" + k + " : (v ?? ";
	m += atoms[i] + "). *)\n\n";
	k = k + 1;
    }

    //console.log(ex.premises);

    var r = ex.premises.map(function (d) { return proofweb(d); });

    //console.log(r);
    //console.log(r.join(' /\\ '));
    //console.log(ex.conclusion);
    //console.log(proofweb(ex.conclusion));
    

    var p = ' ( ( ' + r.join(' /\\ ') + ' ) -> ' + proofweb(ex.conclusion) + ' ) ';

    console.log(p);

    m += "Theorem T" + n + " : (v ||-/- " + p + ").\n\n";

    m += ["Proof.",
	  "(* prop_refute *)",
	  "Qed.\n"].join("\n\n");

    return m;
}

function shuffle(array) {
    var tmp, current, top = array.length;

    if(top) while(--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array;
}


function shuffles(obj1, obj2) {
  var index = obj1.length;
  var rnd, tmp1, tmp2;

  while (index) {
    rnd = Math.floor(Math.random() * index);
    index -= 1;
    tmp1 = obj1[index];
    tmp2 = obj2[index];
    obj1[index] = obj1[rnd];
    obj2[index] = obj2[rnd];
    obj1[rnd] = tmp1;
    obj2[rnd] = tmp2;
  }
}


function combine(res) {
    var arg = res.exercises.valid.concat(res.exercises.invalid);
    
    // var v_args = Array(rte+1).join('1').split('').concat(Array(rte+1).join('0').split(''));
    var a = shuffle(arg);

    // console.log(a);

    var r = {};

    $.each(students, function(i, val) {
        r[val] = { nd: [], sem: [], vld:[]};

        var l = r[val];

        var j;

        var rte = res.request.num_exercises / num_students;

        if( include(res.request.restrictions, 'refutable_provable')){
                for (j = 0; j < rte; j++) {
                        //console.log(i + " " + j);
                        l.nd[j] = natural_ded(res.request.atoms, a[i * rte + j], j);
                        l.sem[j] = semantic(res.request.atoms, a[i * rte + j], j);
                        l.vld[j] = a[i* rte +j].is_valid ? 'valid' : 'invalid';
                }
	}
	if (include(res.request.restrictions, 'only_provable')){
		for (j = 0; j < rte; j++) {
			//console.log(i + " " + j);
			l.nd[j] = natural_ded(res.request.atoms, a[i * rte + j], j);
		}
	}
	if (include(res.request.restrictions, 'only_refutable')){
		for (j = 0; j < rte; j++) {
			//console.log(i + " " + j);
			l.sem[j] = semantic(res.request.atoms, a[i * rte + j], j);
		}
	}
    });
    return {
        code: res.request.code,
        course: res.request.course,
        data: r
        };
};


//console.log(natural_ded(s.request.atoms, s.exercises.valid[0], 0));
//console.log(semantic(s.request.atoms, s.exercises.valid[0], 0));

function select(nome) {
    $('#side .tab').hide();
    $('#side .tab#c_' + nome).show();
    $('#menu .selected').removeClass('selected');
    $('#menu #' + nome).addClass('selected');
}

var test;

$(document).ready(function () {

    $("a#salvar").click(function (ev) {
        $.ajax({
            url:"http://0.0.0.0/src/write.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(combine(test)),
            error: function(obj, status) {
                alert("Error to save the data: \n" + status + obj);
            },
            success: function(data) {
				console.log('---');
            }
        });
    });

    $("a#send").click(function (ev) {
        $('a#send').toggle();
        $('img#load').show();
	    $('a#salvar').hide();

        $.ajax({
            url:"generate.php",
            type: "POST",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            data: JSON.stringify(form_value()),
            error: function(obj, status) {
                alert("Error to submit the data: \n" + status + obj);
            },
            success: function(data) {
                $('#c_detalhes').show().html('<pre>' + JSON.stringify(data, null, 4));
				$('#c_exerc').show().html('<pre>' + JSON.stringify(combine(data), null, 4));
				test = data;
                $('a#send').toggle();
                $('img#load').hide();                
                $('#menu').show();
                $('a#salvar').show();
		
                $('#menu #exerc').trigger('click');
            }
        });
    });

    $('form input').bind('change blur', function () {
        $('textarea[name="codigo"]').val(JSON.stringify(form_value(), null, 4));
    });

    $('#side a').bind('click', function(ev) {
        select(this.id);
    });

    $('input[name=num_exercises]')
        .bind('blur', function (ev) {
            var v = $(this).val() || 0;
            $(this).val(v);
            $('span#num_total').text($(this).val() * num_students);
        })
        .val(def.num_exercises)
        .trigger('blur');
        

$('#tasktype').change(function()
	{
		/* setting currently changed option value to option variable */
		var option = "";
    
		var option = $('#tasktype').val();
		
		/* setting input box value to selected option value */
		if(option == "provable-level1"){
			$('#code').val('Proof_level1');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('2');
			$('#compl_max').val('4');
			$('#num_premises').val('2');
			$('#same_proportion').prop('checked', false);
			$('#relevant').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_provable').attr('checked', 'checked');
		}
		if(option == "provable-level2"){
			$('#code').val('Proof_level2');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('4');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_provable').attr('checked', 'checked');
		}
		if(option == "provable-level3"){
			$('#code').val('Proof_level3');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('5');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_provable').attr('checked', 'checked');
		}
		if(option == "provable-level4"){
			$('#code').val('Proof_level4');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('4');
			$('#compl_max').val('5');
			$('#num_premises').val('4');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_provable').attr('checked', 'checked');
		} 
		/* ONLY REFUTABLE */
		if(option == "refutable-level1"){
			$('#code').val('Refute_level1');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('2');
			$('#compl_max').val('4');
			$('#num_premises').val('2');
			$('#same_proportion').prop('checked', false);
			$('#relevant').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_refutable').attr('checked', 'checked');
		}
		if(option == "refutable-level2"){
			$('#code').val('Refute_level2');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('4');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_refutable').attr('checked', 'checked');
		}
		if(option == "refutable-level3"){
			$('#code').val('Refute_level3');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('5');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_refutable').attr('checked', 'checked');
		}
		if(option == "refutable-level4"){
			$('#code').val('Refute_level4');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('4');
			$('#compl_max').val('5');
			$('#num_premises').val('4');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', false);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#only_refutable').attr('checked', 'checked');
		} 		
		
		/* CONJECTURE TO PROVE or REFUTE */
		if(option == "conjecture-level1"){
			$('#code').val('Conjecture_level1');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('2');
			$('#compl_max').val('4');
			$('#num_premises').val('2');
			$('#same_proportion').prop('checked', true);
			$('#relevant').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#refutable_provable').attr('checked', 'checked');
		}
		if(option == "conjecture-level2"){
			$('#code').val('Conjecture_level2');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('4');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#refutable_provable').attr('checked', 'checked');
		}
		if(option == "conjecture-level3"){
			$('#code').val('Conjecture_level3');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('3');
			$('#compl_max').val('5');
			$('#num_premises').val('3');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#refutable_provable').attr('checked', 'checked');
		}
		if(option == "conjecture-level4"){
			$('#code').val('Conjecture_level4');
			$('#num_exercises').val('2');
			$('#atoms').val('A, B, C, D, E');
			$('#not').prop('checked', true);
			$('#or').prop('checked', true);
			$('#and').prop('checked', true);
			$('#imp').prop('checked', true);
			$('#biimp').prop('checked', true);
			$('#compl_min').val('4');
			$('#compl_max').val('5');
			$('#num_premises').val('4');
			$('#relevant').prop('checked', true);
			$('#same_proportion').prop('checked', true);
			$('#no_superfluous').prop('checked', true);
			$('#premise_contingent').prop('checked', true);
			$('#refutable_provable').attr('checked', 'checked');
		}		
	});        


});

<?php
class Wishlist_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }
	
	function __get_wishlist() {
		$this -> db -> select('* FROM wishlist_tab WHERE (wstatus=1 OR wstatus=0) ORDER BY wid DESC');
		return $this -> db -> get() -> result();
	}

	function __get_wishlistz() {
		$this -> db -> select(" *, (select wname from wishlist_tab where wtid=tid order by wid desc limit 1) as wname, (select person from wishlist_tab where wtid=tid  order by wid desc limit 1) as person FROM tables_tab,categories_tab WHERE  tstatus <>2 and cid=tcid ORDER BY tid DESC");
		return $this -> db -> get() -> result();
	}	
	
	function __last_wishlist_by_wtid($wtid) {
		$queryd = $this -> db -> query("SELECT wid FROM wishlist_tab WHERE wtid='$wtid' and wstatus='1'	ORDER BY wid DESC");
		$queryd = $queryd-> result();
		$wid = $queryd[0] -> wid;
		return $wid;
	}
	
	function __get_wishlistx($wid) {
		$this -> db -> select("a.*,b.*,c.*,(select tname from tables_tab d where d.tid=a.wtid) as tname FROM wishlist_tab a,wishlist_detail_tab b, menus_tab c, categories_tab d WHERE a.wid=b.wid and b.wmid=c.mid and c.mcid=d.cid and d.ctype=1 and (a.wstatus=1 OR a.wstatus=0) AND b.wstatus=1 and a.wid=".$wid." ORDER BY d.cposition ASC, a.wid DESC");
		return $this -> db -> get() -> result();
	}
	
	function __get_wishlist_detail($id) {
		return "SELECT * FROM wishlist_tab WHERE (wstatus='1' OR wstatus='0' ) AND wid='".$id."' ORDER BY wid DESC'";
	}
	
	function __insert_wishlist($data) {
        return $this -> db -> insert('wishlist_tab', $data);
	}
	function __insert_wishlist_detail($data){
		return $this -> db -> insert('wishlist_detail_tab', $data);
	}	
	
	function __update_wishlist($id, $data) {
        $this -> db -> where('wid', $id);
        return $this -> db -> update('wishlist_tab', $data);
	}

	function __cancel_wishlist($id) {
		$uid= $this -> memcachedlib -> sesresult['uid'];
		$wudate = date('Y-m-d h:i:s');
		$data = array('wstatus'=>2,'wupdateby'=>$uid,'wudate'=>$wudate);
        $this -> db -> where('wid', $id);
        return $this -> db -> update('wishlist_tab', $data);
	}	

	function __cancel_table($wtid) {
		$dat = array('tstatus'=>1);
        $this -> db -> where('tid', $wtid);
        return $this -> db -> update('tables_tab', $dat);
	}
	
	function __update_wishlist_detail($id, $data) {
        $this -> db -> where('wdid', $id);
        return $this -> db -> update('wishlist_detail_tab', $data);
	}
	
	function __delete_wishlist_detail($id) {
		return $this -> db -> query('UPDATE wishlist_detail_tab SET wstatus=2 WHERE wdid=' . $id);
	}
	
	function ___get_wishlist_menus() {
		$res = array();
		$this -> db -> select('cid,cname FROM categories_tab WHERE ctype=1 AND cstatus=1 ORDER BY cposition ASC');
		$cat = $this -> db -> get() -> result();
		foreach($cat as $k => $v) {
			$this -> db -> select('mid,mname,mdesc,mdisc,mharga FROM menus_tab WHERE mstatus=1 AND mcid=' . $v -> cid . ' ORDER BY mposition ASC');
			$menus = $this -> db -> get() -> result();
			foreach($menus as $k1 => $v1) {
				$res[$v -> cid][$v -> cname][] = $v1;
			}
			$menus = array();
		}
		return $res;
	}
	
	function __check_order_no() {
		$this -> db -> select("worderno FROM wishlist_order_tab WHERE DATE(FROM_UNIXTIME( wdate, '%Y-%m-%d' ))>=DATE(now()) ORDER BY wid DESC LIMIT 1", FALSE);
		return $this -> db -> get() -> result();
	}
	
	function __insert_wishlist_order($data) {
		return $this -> db -> insert('wishlist_order_tab', $data);
	}
}

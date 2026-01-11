<?php

namespace App\Services;

use App\Models\FamilyMember;

class KinshipService
{
    public function getRelationship(FamilyMember $subject, FamilyMember $object)
    {
        return $this->calculateRelationship($subject, $object);
    }
    
    // 计算两个家族成员之间的亲属关系
    private function calculateRelationship(FamilyMember $subject, FamilyMember $object)
    {
        // 如果是同一个人
        if ($subject->id === $object->id) {
            return '自己';
        }

        // 检查是否是配偶
        if ($this->isSpouse($subject, $object)) {
            return $this->getSpouseRelationship($subject, $object);
        }

        // 检查是否是父母
        if ($this->isParent($subject, $object)) {
            return $this->getParentRelationship($subject, $object);
        }

        // 检查是否是子女
        if ($this->isChild($subject, $object)) {
            return $this->getChildRelationship($subject, $object);
        }

        // 检查是否是兄弟姐妹
        if ($this->isSibling($subject, $object)) {
            return $this->getSiblingRelationship($subject, $object);
        }

        // 检查是否是祖父母
        if ($this->isGrandparent($subject, $object)) {
            return $this->getGrandparentRelationship($subject, $object);
        }

        // 检查是否是孙子女
        if ($this->isGrandchild($subject, $object)) {
            return $this->getGrandchildRelationship($subject, $object);
        }

        // 检查是否是叔叔/伯父/姑姑（父亲的兄弟姐妹）
        if ($this->isPaternalUncleOrAunt($subject, $object)) {
            return $this->getPaternalUncleOrAuntRelationship($subject, $object);
        }

        // 检查是否是舅舅/阿姨（母亲的兄弟姐妹）
        if ($this->isMaternalUncleOrAunt($subject, $object)) {
            return $this->getMaternalUncleOrAuntRelationship($subject, $object);
        }

        // 检查是否是侄子/侄女（兄弟姐妹的子女）
        if ($this->isNephewOrNiece($subject, $object)) {
            return $this->getNephewOrNieceRelationship($subject, $object);
        }

        // 检查是否是外甥/外甥女（姐妹的子女）
        if ($this->isCousin($subject, $object)) {
            return $this->getCousinRelationship($subject, $object);
        }

        // 其他亲属关系可以在这里扩展
        return '亲属关系未定义';
    }

    // 判断是否是配偶
    private function isSpouse(FamilyMember $subject, FamilyMember $object)
    {
        return $subject->spouse_id === $object->id || $object->spouse_id === $subject->id;
    }

    // 获取配偶关系称谓
    private function getSpouseRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($subject->gender === 'male') {
            return '妻子';
        } else {
            return '丈夫';
        }
    }

    // 判断是否是父母
    private function isParent(FamilyMember $subject, FamilyMember $object)
    {
        return $object->father_id === $subject->id || $object->mother_id === $subject->id;
    }

    // 获取父母关系称谓
    private function getParentRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($subject->father_id === $object->id) {
            return '父亲';
        } else {
            return '母亲';
        }
    }

    // 判断是否是子女
    private function isChild(FamilyMember $subject, FamilyMember $object)
    {
        return $subject->father_id === $object->id || $subject->mother_id === $object->id;
    }

    // 获取子女关系称谓
    private function getChildRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($object->gender === 'male') {
            return '儿子';
        } else {
            return '女儿';
        }
    }

    // 判断是否是兄弟姐妹
    private function isSibling(FamilyMember $subject, FamilyMember $object)
    {
        return ($subject->father_id === $object->father_id && $subject->father_id !== null) ||
               ($subject->mother_id === $object->mother_id && $subject->mother_id !== null);
    }

    // 获取兄弟姐妹关系称谓
    private function getSiblingRelationship(FamilyMember $subject, FamilyMember $object)
    {
        // 这里需要考虑年龄顺序，暂时简化处理
        if ($object->gender === 'male') {
            return '兄弟';
        } else {
            return '姐妹';
        }
    }

    // 判断是否是祖父母
    private function isGrandparent(FamilyMember $subject, FamilyMember $object)
    {
        // 祖父：父亲的父亲
        if ($subject->father && $subject->father->father_id === $object->id) {
            return true;
        }
        // 祖母：父亲的母亲
        if ($subject->father && $subject->father->mother_id === $object->id) {
            return true;
        }
        // 外祖父：母亲的父亲
        if ($subject->mother && $subject->mother->father_id === $object->id) {
            return true;
        }
        // 外祖母：母亲的母亲
        if ($subject->mother && $subject->mother->mother_id === $object->id) {
            return true;
        }
        return false;
    }

    // 获取祖父母关系称谓
    private function getGrandparentRelationship(FamilyMember $subject, FamilyMember $object)
    {
        // 祖父：父亲的父亲
        if ($subject->father && $subject->father->father_id === $object->id) {
            return '祖父';
        }
        // 祖母：父亲的母亲
        if ($subject->father && $subject->father->mother_id === $object->id) {
            return '祖母';
        }
        // 外祖父：母亲的父亲
        if ($subject->mother && $subject->mother->father_id === $object->id) {
            return '外祖父';
        }
        // 外祖母：母亲的母亲
        if ($subject->mother && $subject->mother->mother_id === $object->id) {
            return '外祖母';
        }
        return '';
    }

    // 判断是否是孙子女
    private function isGrandchild(FamilyMember $subject, FamilyMember $object)
    {
        foreach ($object->children as $child) {
            if ($child->id === $subject->father_id || $child->id === $subject->mother_id) {
                return true;
            }
        }
        return false;
    }

    // 获取孙子女关系称谓
    private function getGrandchildRelationship(FamilyMember $subject, FamilyMember $object)
    {
        foreach ($object->children as $child) {
            if ($child->id === $subject->father_id || $child->id === $subject->mother_id) {
                if ($subject->gender === 'male') {
                    return '孙子';
                } else {
                    return '孙女';
                }
            }
        }
        return '';
    }
    
    // 判断是否是父亲的兄弟姐妹（叔叔/伯父/姑姑）
    private function isPaternalUncleOrAunt(FamilyMember $subject, FamilyMember $object)
    {
        if (!$subject->father) {
            return false;
        }
        return $this->isSibling($subject->father, $object);
    }
    
    // 获取父亲的兄弟姐妹关系称谓
    private function getPaternalUncleOrAuntRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($object->gender === 'male') {
            // 父亲的哥哥：伯父，父亲的弟弟：叔叔
            return $object->id > $subject->father->id ? '叔叔' : '伯父';
        } else {
            return '姑姑';
        }
    }
    
    // 判断是否是母亲的兄弟姐妹（舅舅/阿姨）
    private function isMaternalUncleOrAunt(FamilyMember $subject, FamilyMember $object)
    {
        if (!$subject->mother) {
            return false;
        }
        return $this->isSibling($subject->mother, $object);
    }
    
    // 获取母亲的兄弟姐妹关系称谓
    private function getMaternalUncleOrAuntRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($object->gender === 'male') {
            return '舅舅';
        } else {
            return '阿姨';
        }
    }
    
    // 判断是否是侄子/侄女（兄弟姐妹的子女）
    private function isNephewOrNiece(FamilyMember $subject, FamilyMember $object)
    {
        // 兄弟姐妹的子女
        if ($subject->father) {
            foreach ($subject->father->children() as $sibling) {
                if ($sibling->id !== $subject->id) {
                    foreach ($sibling->children as $nephew) {
                        if ($nephew->id === $object->id) {
                            return true;
                        }
                    }
                }
            }
        }
        
        if ($subject->mother) {
            foreach ($subject->mother->children() as $sibling) {
                if ($sibling->id !== $subject->id) {
                    foreach ($sibling->children as $nephew) {
                        if ($nephew->id === $object->id) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    // 获取侄子/侄女关系称谓
    private function getNephewOrNieceRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($object->gender === 'male') {
            return '侄子';
        } else {
            return '侄女';
        }
    }
    
    // 获取外甥/外甥女关系称谓
    private function getNephewOrNieceFromSisterRelationship(FamilyMember $subject, FamilyMember $object)
    {
        if ($object->gender === 'male') {
            return '外甥';
        } else {
            return '外甥女';
        }
    }
    
    // 判断是否是堂兄弟姐妹/表兄弟姐妹
    private function isCousin(FamilyMember $subject, FamilyMember $object)
    {
        // 检查是否是父亲的兄弟姐妹的子女（堂兄弟姐妹）
        if ($subject->father) {
            foreach ($subject->father->children() as $paternalSibling) {
                if ($paternalSibling->id !== $subject->father->id) {
                    foreach ($paternalSibling->children as $paternalCousin) {
                        if ($paternalCousin->id === $object->id) {
                            return true;
                        }
                    }
                }
            }
        }
        
        // 检查是否是母亲的兄弟姐妹的子女（表兄弟姐妹）
        if ($subject->mother) {
            foreach ($subject->mother->children() as $maternalSibling) {
                if ($maternalSibling->id !== $subject->mother->id) {
                    foreach ($maternalSibling->children as $maternalCousin) {
                        if ($maternalCousin->id === $object->id) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    // 获取堂兄弟姐妹/表兄弟姐妹关系称谓
    private function getCousinRelationship(FamilyMember $subject, FamilyMember $object)
    {
        // 父亲的兄弟姐妹的子女：堂兄弟姐妹
        if ($subject->father) {
            foreach ($subject->father->children() as $paternalSibling) {
                if ($paternalSibling->id !== $subject->father->id) {
                    foreach ($paternalSibling->children as $paternalCousin) {
                        if ($paternalCousin->id === $object->id) {
                            return $object->gender === 'male' ? '堂兄弟' : '堂姐妹';
                        }
                    }
                }
            }
        }
        
        // 母亲的兄弟姐妹的子女：表兄弟姐妹
        if ($subject->mother) {
            foreach ($subject->mother->children() as $maternalSibling) {
                if ($maternalSibling->id !== $subject->mother->id) {
                    foreach ($maternalSibling->children as $maternalCousin) {
                        if ($maternalCousin->id === $object->id) {
                            return $object->gender === 'male' ? '表兄弟' : '表姐妹';
                        }
                    }
                }
            }
        }
        
        return '堂/表兄弟姐妹';
    }

    // 获取家族成员的所有亲属
    public function getAllRelatives(FamilyMember $member)
    {
        $relatives = [];

        // 配偶
        if ($member->spouse) {
            $relatives[] = [
                'member' => $member->spouse,
                'relationship' => $this->getSpouseRelationship($member, $member->spouse)
            ];
        }

        // 父母
        if ($member->father) {
            $relatives[] = [
                'member' => $member->father,
                'relationship' => '父亲'
            ];
        }
        if ($member->mother) {
            $relatives[] = [
                'member' => $member->mother,
                'relationship' => '母亲'
            ];
        }

        // 子女
        foreach ($member->children as $child) {
            $relatives[] = [
                'member' => $child,
                'relationship' => $this->getChildRelationship($member, $child)
            ];
        }

        // 兄弟姐妹
        $siblings = FamilyMember::where(function($query) use ($member) {
            $query->where('father_id', $member->father_id)
                  ->orWhere('mother_id', $member->mother_id);
        })->where('id', '!=', $member->id)
          ->where('user_id', $member->user_id)
          ->get();

        foreach ($siblings as $sibling) {
            $relatives[] = [
                'member' => $sibling,
                'relationship' => $this->getSiblingRelationship($member, $sibling)
            ];
        }

        // 祖父母
        if ($member->father && $member->father->father) {
            $relatives[] = [
                'member' => $member->father->father,
                'relationship' => '祖父'
            ];
        }
        if ($member->father && $member->father->mother) {
            $relatives[] = [
                'member' => $member->father->mother,
                'relationship' => '祖母'
            ];
        }
        if ($member->mother && $member->mother->father) {
            $relatives[] = [
                'member' => $member->mother->father,
                'relationship' => '外祖父'
            ];
        }
        if ($member->mother && $member->mother->mother) {
            $relatives[] = [
                'member' => $member->mother->mother,
                'relationship' => '外祖母'
            ];
        }

        // 孙子女
        foreach ($member->children as $child) {
            foreach ($child->children as $grandchild) {
                $relatives[] = [
                    'member' => $grandchild,
                    'relationship' => $grandchild->gender === 'male' ? '孙子' : '孙女'
                ];
            }
        }
        
        // 父亲的兄弟姐妹（叔叔/伯父/姑姑）
        if ($member->father) {
            foreach ($member->father->children() as $paternalSibling) {
                if ($paternalSibling->id !== $member->father->id) {
                    $relatives[] = [
                        'member' => $paternalSibling,
                        'relationship' => $paternalSibling->gender === 'male' 
                            ? ($paternalSibling->id > $member->father->id ? '叔叔' : '伯父') 
                            : '姑姑'
                    ];
                }
            }
        }
        
        // 母亲的兄弟姐妹（舅舅/阿姨）
        if ($member->mother) {
            foreach ($member->mother->children() as $maternalSibling) {
                if ($maternalSibling->id !== $member->mother->id) {
                    $relatives[] = [
                        'member' => $maternalSibling,
                        'relationship' => $maternalSibling->gender === 'male' ? '舅舅' : '阿姨'
                    ];
                }
            }
        }
        
        // 侄子/侄女（兄弟姐妹的子女）
        $allSiblings = [];
        if ($member->father) {
            $allSiblings = array_merge($allSiblings, $member->father->children()->where('id', '!=', $member->id)->get()->toArray());
        }
        if ($member->mother) {
            $allSiblings = array_merge($allSiblings, $member->mother->children()->where('id', '!=', $member->id)->get()->toArray());
        }
        
        // 去重
        $allSiblings = array_unique($allSiblings, SORT_REGULAR);
        
        foreach ($allSiblings as $sibling) {
            $siblingModel = FamilyMember::find($sibling['id']);
            foreach ($siblingModel->children as $nephew) {
                $relatives[] = [
                    'member' => $nephew,
                    'relationship' => $nephew->gender === 'male' ? '侄子' : '侄女'
                ];
            }
        }
        
        // 堂兄弟姐妹/表兄弟姐妹
        if ($member->father) {
            foreach ($member->father->children() as $paternalUncle) {
                if ($paternalUncle->id !== $member->father->id) {
                    foreach ($paternalUncle->children as $cousin) {
                        $relatives[] = [
                            'member' => $cousin,
                            'relationship' => $cousin->gender === 'male' ? '堂兄弟' : '堂姐妹'
                        ];
                    }
                }
            }
        }
        
        if ($member->mother) {
            foreach ($member->mother->children() as $maternalUncle) {
                if ($maternalUncle->id !== $member->mother->id) {
                    foreach ($maternalUncle->children as $cousin) {
                        $relatives[] = [
                            'member' => $cousin,
                            'relationship' => $cousin->gender === 'male' ? '表兄弟' : '表姐妹'
                        ];
                    }
                }
            }
        }

        return $relatives;
    }
}
